<!DOCTYPE html>
<html>
<head>
    <title>Item List</title> <!-- Menentukan judul halaman -->
</head>
<body>
    <h1>Items</h1> <!-- Menampilkan judul halaman -->

    <!-- Menampilkan pesan sukses jika ada -->
    @if(session('success'))  
        <p>{{ session('success') }}</p> <!-- Menampilkan pesan sukses dari session -->
    @endif

    <!-- Link untuk menambah item baru -->
    <a href="{{ route('items.create') }}">Add Item</a>

    <ul>
        <!-- Looping melalui semua item yang ada -->
        @foreach ($items as $item)
            <li>
                {{ $item->name }} -  <!-- Menampilkan nama item -->

                <!-- Link untuk mengedit item -->
                <a href="{{ route('items.edit', $item) }}">Edit</a>

                <!-- Form untuk menghapus item -->
                <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;">
                    @csrf <!-- Menambahkan CSRF token untuk keamanan -->
                    @method('DELETE') <!-- Mengubah metode request menjadi DELETE -->
                    <button type="submit">Delete</button> <!-- Tombol untuk menghapus item -->
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
