<?php
include 'db.php';

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $stmt = $pdo->prepare("INSERT INTO public.todo (task, description, completed) VALUES (?, ?, false)");
    $stmt->execute([$title, $description]);
    echo json_encode(['status' => 'success']);
}

elseif ($action === 'fetch') {
    $stmt = $pdo->query("SELECT * FROM public.todo ORDER BY id DESC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tasks);
}

elseif ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM public.todo WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'deleted']);
}

elseif ($action === 'toggle') {
    $id = $_POST['id'];
    $completed = $_POST['completed'] === 'true' ? 'true' : 'false';
    $stmt = $pdo->prepare("UPDATE public.todo SET completed = $completed WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'updated']);
}

elseif ($action === 'edit') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM public.todo WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        echo json_encode($task);
    } else {
        echo json_encode(['error' => 'Task not found']);
    }
}

elseif ($action === 'update') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $stmt = $pdo->prepare("UPDATE public.todo SET task = ?, description = ? WHERE id = ?");
    $stmt->execute([$title, $description, $id]);
    echo json_encode(['status' => 'updated']);
}

elseif ($action === 'bulk_complete') {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        // Gunakan placeholder dinamis untuk keamanan
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE public.todo SET completed = TRUE WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        echo json_encode(['status' => 'bulk_completed']);
    }
}

elseif ($action === 'bulk_delete') {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("DELETE FROM public.todo WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        echo json_encode(['status' => 'bulk_deleted']);
    }
}
?>
