<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Viemo Video Player</title>
</head>
<body>
    <iframe src="https://player.vimeo.com/video/{{ $id }}" 
                            frameborder="0" 
                            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; gyroscope; accelerometer" 
                            style="position:absolute;top:0;left:0;width:100%;height:100%;" 
                            title="video_20240822_142621"></iframe>
    <script src="https://player.vimeo.com/api/player.js"></script>
</body>
</html>