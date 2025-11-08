<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar Penjualan yang terdaftar di sistem'
        ];

        $activeMenu = 'penjualan';

        $penjualan = PenjualanModel::with('user')->get(); // Ambil semua penjualan dengan relasi user
        $barang = BarangModel::all(); // ambil semua barang
        $user = UserModel::all(); // ambil semua user
        $penjualanDetail = PenjualanDetailModel::with('penjualan.user')->get();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'penjualan' => $penjualan,
            'barang' => $barang,
            'user' => $user,
            'penjualanDetail' => $penjualanDetail
        ]);
    }

    public function list(Request $request)
    {
        $penjualanDetail = PenjualanDetailModel::with('penjualan', 'penjualan.user', 'barang');

        if ($request->filter_barang) {
            $penjualanDetail->where('barang_id', $request->filter_barang);
        }

        if ($request->filter_kode_penjualan) {
            $penjualanDetail->where('penjualan_id', $request->filter_kode_penjualan);
        }

        return DataTables::of($penjualanDetail)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualanDetail) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualanDetail->detail_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualanDetail->detail_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualanDetail->detail_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $user = UserModel::select('user_id', 'nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')->get();
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'pembeli')->get();

        return view('penjualan.create_ajax', compact('user', 'barang', 'penjualan'));
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'total_harga' => 'required|numeric',
            'detail' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors(),
                'message' => 'Validasi gagal.',
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualan = PenjualanModel::create([
                'tanggal' => $request->tanggal,
                'total_harga' => $request->total_harga,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->detail as $item) {
                $barang = BarangModel::findOrFail($item['barang_id']);

                // Validasi stok cukup
                if ($barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok untuk {$barang->barang_nama} tidak mencukupi.");
                }

                // Kurangi stok
                $barang->stok -= $item['jumlah'];
                $barang->save();

                // Simpan detail penjualan
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $barang->harga_jual * $item['jumlah'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil disimpan dan stok diperbarui.',
                'redirect' => url('penjualan'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit_ajax($id)
    {
        $penjualanDetail = PenjualanDetailModel::findOrFail($id);
        $penjualan = PenjualanModel::all();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'nama')->get();

        return view('penjualan.edit_ajax', compact('penjualanDetail', 'penjualan', 'barang', 'user'));
    }

    public function update_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'detail_id' => 'required|integer',
            'barang_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors(),
                'message' => 'Validasi gagal.',
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualanDetail = PenjualanDetailModel::findOrFail($request->detail_id);

            // Kembalikan stok lama
            $barangLama = BarangModel::find($penjualanDetail->barang_id);
            $barangLama->stok += $penjualanDetail->jumlah;
            $barangLama->save();

            // Ambil barang baru dari request
            $barangBaru = BarangModel::findOrFail($request->barang_id);

            // Cek stok baru cukup
            if ($barangBaru->stok < $request->jumlah) {
                throw new \Exception("Stok untuk {$barangBaru->barang_nama} tidak mencukupi.");
            }

            // Kurangi stok baru
            $barangBaru->stok -= $request->jumlah;
            $barangBaru->save();

            // Update detail penjualan
            $penjualanDetail->update([
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga' => $barangBaru->harga_jual * $request->jumlah,
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Detail penjualan berhasil diperbarui dan stok disesuaikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ]);
        }
    }

    public function confirm_ajax($id)
    {
        $penjualanDetail = PenjualanDetailModel::find($id);

        // Pastikan penjualanDetail ditemukan
        if (!$penjualanDetail) {
            // Jika data tidak ditemukan, bisa mengembalikan response error atau mengarahkan ke halaman lain
            return redirect()->back()->with('error', 'Detail penjualan tidak ditemukan');
        }

        // Mengirim data penjualanDetail ke view
        return view('penjualan.confirm_ajax', ['penjualanDetail' => $penjualanDetail]);
    }

    public function delete_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'detail_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors(),
                'message' => 'Validasi gagal.',
            ]);
        }

        DB::beginTransaction();
        try {
            $penjualanDetail = PenjualanDetailModel::findOrFail($request->detail_id);

            // Kembalikan stok
            $barang = BarangModel::find($penjualanDetail->barang_id);
            if ($barang) {
                $barang->stok += $penjualanDetail->jumlah;
                $barang->save();
            }

            $penjualanDetail->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Detail penjualan berhasil dihapus dan stok dikembalikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ]);
        }
    }

    public function show_ajax(string $id)
    {
        $penjualanDetail = PenjualanDetailModel::with('penjualan.user', 'barang')->find($id);

        return view('penjualan.show_ajax', compact('penjualanDetail'));
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // Validasi file harus xls atau xlsx, max 1MB
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            
            $file = $request->file('file_penjualan'); // ambil file dari request
            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
            $data = $sheet->toArray(null, false, true, true); // ambil data excel
            
            $insertDetail = [];
            $existingPenjualan = [];

            if (count($data) > 1) { // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $kode = $value['B'];

                        // Jika belum ada, simpan penjualan master-nya
                        if (!isset($existingPenjualan[$kode])) {
                            $penjualan = PenjualanModel::firstOrCreate(
                                ['penjualan_kode' => $kode],
                                [   'user_id' => $value['A'],
                                    'penjualan_tanggal' => $value['C'],
                                    'pembeli' => $value['D'],]
                            );
                            $existingPenjualan[$kode] = $penjualan->penjualan_id;
                        }
                        
                        $barang_id = $value['E'];
                        $jumlah = $value['F'];

                        // Ambil data barang dari DB untuk ambil harga_jual
                        $barang = BarangModel::findOrFail($barang_id);
                        
                        // Tambah ke detail penjualan
                        $insertDetail[] = [
                            'penjualan_id' => $existingPenjualan[$kode],
                            'barang_id' => $barang_id,
                            'jumlah' => $jumlah,
                            'harga' => $barang->harga_jual * $jumlah,
                            'created_at' => now(),
                        ];
                    }
                }

                // Insert detail penjualan, jika ada data yang valid
                if (count($insertDetail) > 0) {
                    PenjualanDetailModel::insert($insertDetail);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        // ambil data stok yang akan di export
        $penjualanDetail = PenjualanDetailModel::select(
            'detail_id', 
            'penjualan_id',
            'barang_id',
            'jumlah',
            'harga'
            )
        ->orderBy('detail_id')
        ->with('penjualan', 'penjualan.user', 'barang') // pastikan relasi didefinisikan
        ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Id Detail');
        $sheet->setCellValue('C1', 'Id Penjualan');
        $sheet->setCellValue('D1', 'Nama Penjual');
        $sheet->setCellValue('E1', 'Kode Penjualan');
        $sheet->setCellValue('F1', 'Tanggal Penjualan');
        $sheet->setCellValue('G1', 'Nama Pembeli');
        $sheet->setCellValue('H1', 'Nama Barang');
        $sheet->setCellValue('I1', 'Jumlah Barang');
        $sheet->setCellValue('J1', 'Total Harga');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $no = 1;
        $baris = 2;
        foreach ($penjualanDetail as $item) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $item->detail_id);
            $sheet->setCellValue('C' . $baris, $item->penjualan->penjualan_id);
            $sheet->setCellValue('D' . $baris, $item->penjualan->user->nama);
            $sheet->setCellValue('E' . $baris, $item->penjualan->penjualan_kode);
            $sheet->setCellValue('F' . $baris, $item->penjualan->penjualan_tanggal);
            $sheet->setCellValue('G' . $baris, $item->penjualan->pembeli);
            $sheet->setCellValue('H' . $baris, $item->barang->barang_nama);
            $sheet->setCellValue('I' . $baris, $item->jumlah);
            $sheet->setCellValue('J' . $baris, $item->harga);
            $baris++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Ambil data penjualan detail dengan relasi
        $penjualanDetail = PenjualanDetailModel::select(
            'detail_id', 
            'penjualan_id',
            'barang_id',
            'jumlah',
            'harga'
            )
        ->orderBy('detail_id')
        ->with('penjualan', 'penjualan.user', 'barang') // pastikan relasi didefinisikan
        ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualanDetail]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}