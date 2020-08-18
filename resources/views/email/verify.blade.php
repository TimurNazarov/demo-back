<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div class="email">
        <p>@lang('mail.verify', ['name' => $user->name, 'website_name' => $website_name, 'link' => $link])</p>
    </div>
</body>
</html>