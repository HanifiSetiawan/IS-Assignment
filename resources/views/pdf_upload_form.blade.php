@include('navbar')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload and Sign PDF</title>
</head>
<body>
    <form action="{{ route('pdf.signAndDownload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <label for="file">Choose a PDF file:</label>
        <input type="file" name="file" accept=".pdf" required>
        <button type="submit">Sign and Download</button>
    </form>
</body>
</html>
