<!DOCTYPE html>
<html>
<head>
    <title>Edit Item</title> <!-- Menentukan judul halaman -->
</head>
<body>
    <h1>Edit Item</h1> <!-- Menampilkan judul halaman -->

    <!-- Form untuk mengupdate item, mengirim data ke route 'items.update' -->
    <form action="{{ route('items.update', $item) }}" method="POST">
        @csrf <!-- Menambahkan CSRF token untuk keamanan -->
        @method('PUT') <!-- Mengubah metode request menjadi PUT (karena HTML hanya mendukung GET dan POST) -->
        
        <!-- Input untuk nama item -->
        <label for="name">Name:</label>
        <input type="text" name="name" value="{{ $item->name }}" required>
        <br>
        
        <!-- Input untuk deskripsi item -->
        <label for="description">Description:</label>
        <textarea name="description" required>{{ $item->description }}</textarea>
        <br>
        
        <!-- Tombol untuk mengirim form -->
        <button type="submit">Update Item</button>
    </form>

    <!-- Link untuk kembali ke daftar item -->
    <a href="{{ route('items.index') }}">Back to List</a>
</body>
</html>
