<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use App\Models\PenjualanDetailModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object) [
            'title' => 'Daftar Stok yang terdaftar di sistem'
        ];

        $activeMenu = 'stok';

        $barang = BarangModel::all(); // ambil semua barang
        $user = UserModel::all(); // ambil semua user
        $penjualan = PenjualanDetailModel::with('barang')->get();

        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function list(Request $request)
    {
        $stok = StokModel::with('barang', 'user', 'penjualan.barang');

        if ($request->filter_barang) {
            $stok->where('barang_id', $request->filter_barang);
        }

        if ($request->filter_tanggal) {
            $query->whereDate('stok_tanggal', $request->filter_tanggal);
        }        

        return DataTables::of($stok)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'nama')->get();

        return view('stok.create_ajax')->with('barang', $barang)->with('user', $user);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id'     => 'required|exists:m_barang,barang_id',
                'user_id'       => 'required|exists:m_user,user_id',
                'stok_tanggal'  => 'required|date',
                'stok_jumlah'   => 'required|numeric|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $data = $request->all();

            // ubah dari "YYYY-MM-DDTH:i" ke "YYYY-MM-DD H:i:s"
            $data['stok_tanggal'] = Carbon::parse($request->stok_tanggal)->format('Y-m-d H:i:s');

            StokModel::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data Stok berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax($id)
    {
        $stok = StokModel::findOrFail($id);
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'nama')->get();

        return view('stok.edit_ajax', compact('stok','barang', 'user'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id'     => 'required|exists:m_barang,barang_id',
                'user_id'       => 'required|exists:m_user,user_id',
                'stok_tanggal'  => 'required|date',
                'stok_jumlah'   => 'required|numeric|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $data = $request->all();

            // ubah dari "YYYY-MM-DDTH:i" ke "YYYY-MM-DD H:i:s"
            $data['stok_tanggal'] = Carbon::parse($request->stok_tanggal)->format('Y-m-d H:i:s');

            StokModel::find($id)->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Data Stok berhasil diubah'
            ]);
        }
        redirect('/');
    }

    public function confirm_ajax($id)
    {
        $stok = StokModel::find($id);

        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);

            if ($stok) {
                try {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data stok gagal dihapus karena ada relasi dengan tabel lain.'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $stok = StokModel::with('barang', 'user')->findOrFail($id);
        return view('stok.show_ajax', ['stok' => $stok]);
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            $file = $request->file('file_stok'); // ambil file dari request
            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
            $data = $sheet->toArray(null, false, true, true); // ambil data excel
            $insert = [];
            if (count($data) > 1) {
                $barangIds = [];
            
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $barangIds[] = $value['A']; // kumpulkan semua barang_id
            
                        $insert[] = [
                            'barang_id' => $value['A'],
                            'user_id' => $value['B'],
                            'stok_tanggal' => $value['C'],
                            'stok_jumlah' => $value['D'],
                            'created_at' => now(),
                        ];
                    }
                }
            
                if (count($insert) > 0) {
                    // Hapus data lama berdasarkan barang_id yang sama
                    StokModel::whereIn('barang_id', $barangIds)->delete();
            
                    // Insert data baru
                    StokModel::insert($insert);
                }
            
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport (data lama diganti)'
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
        $stok = StokModel::select('stok_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('stok_id')
            ->with('barang', 'user')
            ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Id Stok');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Nama Penyetok');
        $sheet->setCellValue('E1', 'Tanggal Stok');
        $sheet->setCellValue('F1', 'Jumlah Stok');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($stok as $item) {
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $item->stok_id);
            $sheet->setCellValue('C' . $baris, $item->barang->barang_nama);
            $sheet->setCellValue('D' . $baris, $item->user->nama);
            $sheet->setCellValue('E' . $baris, $item->stok_tanggal);
            $sheet->setCellValue('F' . $baris, $item->stok_jumlah);
            $baris++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Stok');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $stok = StokModel::select('stok_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
        ->orderBy('stok_id')
        ->with('barang', 'user')
        ->get();
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render(); // Render the PDF as HTML - uncomment if you want to see the HTML outputw

        return $pdf->stream('Data Stok' . date('Y-m-d H:i:s') . '.pdf');
    }
}