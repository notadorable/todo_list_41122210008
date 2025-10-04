<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">My To-Do List</h5>
                </div>
                <div class="card-body">
                    <form id="todoForm">
                        <div class="mb-3">
                            <label class="form-label">Tugas</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Tambah Tugas</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daftar Tugas</h6>
                    <div class="d-flex gap-2">
                        <button id="bulkComplete" class="btn btn-sm btn-light text-success border-success">Selesaikan Terpilih</button>
                        <button id="bulkDelete" class="btn btn-sm btn-light text-danger border-danger">Hapus Terpilih</button>
                    </div>
                </div>
                <ul id="taskList" class="list-group list-group-flush"></ul>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
            <input type="hidden" id="editId">
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" id="editTitle" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" id="editDescription" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    loadTasks();

    // Tambah tugas
    $('#todoForm').on('submit', function(e){
        e.preventDefault();
        $.post('actions.php', {
            action: 'add',
            title: $('#title').val(),
            description: $('#description').val()
        }, function(){
            $('#title').val('');
            $('#description').val('');
            loadTasks();
        });
    });

    // Ambil dan tampilkan daftar tugas
    function loadTasks(){
        $.post('actions.php', { action: 'fetch' }, function(data){
            const tasks = JSON.parse(data || '[]');
            let html = '';

            if (tasks.length === 0) {
                html = `
                    <li class="list-group-item text-center text-muted">
                        Belum ada tugas
                    </li>`;
            } else {
                tasks.forEach(t => {
                    html += `
                        <li class="list-group-item align-items-start d-flex justify-content-between ${t.completed ? 'list-group-item-success' : ''}">
                            <div class="d-flex align-items-start" style="gap: 8px;">
                                <input type="checkbox" class="form-check-input mt-1 selectTask" value="${t.id}">
                                <div>
                                    <div class="fw-bold ${t.completed ? 'text-decoration-line-through' : ''}">
                                        ${t.task || t.title}
                                    </div>
                                    <small>${t.description || ''}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <button class="btn btn-sm btn-outline-success toggle" data-id="${t.id}" data-completed="${!t.completed}">
                                    ${t.completed ? 'Batal' : 'Selesai'}
                                </button>
                                <button class="btn btn-sm btn-outline-primary edit" data-id="${t.id}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete" data-id="${t.id}">Hapus</button>
                            </div>
                        </li>`;
                });
            }

            $('#taskList').html(html);
        });
    }

    // Hapus tugas
    $(document).on('click', '.delete', function(){
        const id = $(this).data('id');
        $.post('actions.php', { action: 'delete', id }, () => loadTasks());
    });

    // Toggle selesai
    $(document).on('click', '.toggle', function(){
        const id = $(this).data('id');
        const completed = $(this).data('completed');
        $.post('actions.php', { action: 'toggle', id, completed }, () => loadTasks());
    });

    // Buka modal edit
    $(document).on('click', '.edit', function(){
        const id = $(this).data('id');
        $.post('actions.php', { action: 'edit', id }, function(data){
            const t = JSON.parse(data);
            $('#editId').val(t.id);
            $('#editTitle').val(t.task || t.title);
            $('#editDescription').val(t.description || '');
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        });
    });

    // Simpan hasil edit
    $('#editForm').on('submit', function(e){
        e.preventDefault();
        $.post('actions.php', {
            action: 'update',
            id: $('#editId').val(),
            title: $('#editTitle').val(),
            description: $('#editDescription').val()
        }, function(){
            const modalEl = document.getElementById('editModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            loadTasks();
        });
    });

    // BULK: selesaikan terpilih
    $('#bulkComplete').click(function(){
        const selected = $('.selectTask:checked').map(function(){ return $(this).val(); }).get();
        if (selected.length === 0) return alert('Pilih minimal satu tugas.');
        $.post('actions.php', { action: 'bulk_complete', ids: selected }, function(){
            loadTasks();
        });
    });

    // BULK: hapus terpilih
    $('#bulkDelete').click(function(){
        const selected = $('.selectTask:checked').map(function(){ return $(this).val(); }).get();
        if (selected.length === 0) return alert('Pilih minimal satu tugas.');
        if (!confirm('Yakin ingin menghapus tugas terpilih?')) return;
        $.post('actions.php', { action: 'bulk_delete', ids: selected }, function(){
            loadTasks();
        });
    });
});
</script>
</body>
</html>
