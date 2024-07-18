<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Contacts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-dark navbar-expand-lg bg-dark bg-gradient">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">CI4 Simple CRUD</a>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto">
                    <?php if (!session()->get('isLoggedIn')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('register') ?>">Register</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <?php if (!empty($list)): ?>
            <div class="card card-outline card-primary rounded-0">
                <div class="card-header">
                    <h4 class="mb-0">List of Contacts</h4>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <table class="table table-striped table-bordered">
                            <colgroup>
                                <col width="10%">
                                <col width="40%">
                                <col width="40%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr class="bg-gradient bg-primary text-light">
                                    <th class="py-1 text-center">#</th>
                                    <th class="py-1 text-center">Name</th>
                                    <th class="py-1 text-center">Gender</th>
                                    <th class="py-1 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($list as $row): ?>
                                    <tr>
                                        <td class="p-1 align-middle text-center"><?= $i++ ?></td>
                                        <td class="p-1 align-middle"><?= htmlspecialchars($row['lastname']) . ", " . htmlspecialchars($row['firstname']) . (!empty($row['middlename']) ? " " . htmlspecialchars($row['middlename']) : '') ?></td>
                                        <td class="p-1 align-middle"><?= htmlspecialchars($row['gender']) ?></td>
                                        <td class="p-1 align-middle text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('main/view/' . $row['id']) ?>" class="btn btn-default bg-gradient-light border text-dark rounded-0" title="View Contact"><i class="fa fa-eye"></i></a>
                                                <a href="<?= base_url('main/edit/' . $row['id']) ?>" class="btn btn-primary rounded-0" title="Edit Contact"><i class="fa fa-edit"></i></a>
                                                <a href="<?= base_url('main/delete/' . $row['id']) ?>" onclick="if(confirm('Are you sure to delete this contact details?') === false) event.preventDefault()" class="btn btn-danger rounded-0" title="Delete Contact"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>No contacts found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
