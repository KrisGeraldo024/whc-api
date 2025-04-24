<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated PDF</title>
</head>
<body style="font-family: Arial, sans-serif;">
   
    <div style="text-align:center;">
    @foreach ($imageArray as $image)
        <img src="{{ $image['path'] }}" alt="{{ $image['alt'] }}" style="max-width: 90%; height: auto;">
    @endforeach
    </div>
</body>
</html>
