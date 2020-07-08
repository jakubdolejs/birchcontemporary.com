<h1>Welcome</h1>
<p><a href="/admin/exhibition/create">Add an exhibition</a></p>
<p><a href="/admin/artists">View and add artists</a></p>
<p><a href="/admin/images">Upload images</a></p>
<?php if (!empty($user) && $user["superuser"]): ?>
<p><a href="/admin/users">Manage users</a></p>
<?php endif; ?>