<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="/api/add/paper" method="POST" enctype="multipart/form-data" >
    <input type="text" name="title" value="werwe">
    <input type="text" name="note" value="ascas">
    <input type="text" name="doc_date" value="7676">
    <input type="text" name="company_id" value="1">
    <input type="file" name="image[]" multiple>
    <button type="submit">click</button>
    
    </form>
</body>
</html>