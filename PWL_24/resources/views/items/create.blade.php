<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title> <!-- Menentukan judul halaman -->
</head>
<body>
    <h1>Add Item</h1> <!-- Menampilkan judul halaman -->

    <!-- Form untuk menambahkan item baru, mengirim data ke route 'items.store' -->
    <form action="{{ route('items.store') }}" method="POST">
        @csrf <!-- Menambahkan CSRF token untuk keamanan -->
        
        <!-- Input untuk nama item -->
        <label for="name">Name:</label>
        <input type="text" name="name" required> <!-- Input wajib diisi -->
        <br>

        <!-- Input untuk deskripsi item -->
        <label for="description">Description:</label>
        <textarea name="description" required></textarea> <!-- Input wajib diisi -->
        <br>

        <!-- Tombol untuk mengirim form -->
        <button type="submit">Add Item</button>
    </form>

    <!-- Link untuk kembali ke daftar item -->
    <a href="{{ route('items.index') }}">Back to List</a>
</body>
</html>
