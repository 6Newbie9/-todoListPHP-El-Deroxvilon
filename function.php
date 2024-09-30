<?php
session_start();

// Inisialisasi session jika belum ada task yang disimpan
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Fungsi untuk menambah task
if (isset($_POST['add_task'])) {
    $errors = [];
    
    $task = trim($_POST['task']);
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $description = trim($_POST['description']);

    // Validasi deskripsi tidak lebih dari 10 kata atau 50 karakter
    $description_words = explode(' ', $description);
    if (count($description_words) > 10 || strlen($description) > 50) {
        $errors[] = 'Deskripsi tidak boleh melebihi 10 kata atau 50 karakter.';
    }

    // Validasi tidak bisa menginput tanggal sebelum hari ini
    $today = date('Y-m-d');
    if (strtotime($deadline) < strtotime($today)) {
        $errors[] = 'Deadline tidak boleh ditanggal yang sudah terlewat.';
    }

    // Memproses deskripsi untuk hanya menyimpan 10 kata
    if (count($description_words) > 10) {
        $description = implode(' ', array_slice($description_words, 0, 10)) . '...';
    }

    // Jika tidak ada error, simpan task
    if (empty($errors)) {
        $new_task = [
            'task' => $task,
            'priority' => $priority,
            'deadline' => $deadline,
            'description' => $description
        ];
        $_SESSION['tasks'][] = $new_task;

        // Set message sukses
        $_SESSION['message'] = 'Task berhasil ditambahkan!';
        $_SESSION['type'] = 'success';
    } else {
        // Set message error
        $_SESSION['message'] = implode(' ', $errors);
        $_SESSION['type'] = 'danger';
    }
}

// Fungsi untuk mengedit task
if (isset($_POST['edit_task'])) {
    $errors = [];
    $index = $_POST['task_index'];
    $task = trim($_POST['task']);
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $description = trim($_POST['description']);

    // Validasi deskripsi tidak lebih dari 10 kata atau 50 karakter
    $description_words = explode(' ', $description);
    if (count($description_words) > 10 || strlen($description) > 60) {
        $errors[] = 'Deskripsi tidak boleh melebihi 10 kata atau 50 karakter.';
    }

    // Validasi tidak bisa menginput tanggal sebelum hari ini
    $today = date('Y-m-d');
    if (strtotime($deadline) < strtotime($today)) {
        $errors[] = 'Deadline tidak boleh ditanggal yang sudah terlewat.';
    }

    // Memproses deskripsi untuk hanya menyimpan 10 kata
    if (count($description_words) > 10) {
        $description = implode(' ', array_slice($description_words, 0, 10)) . '...';
    }

    // Jika tidak ada error, simpan perubahan task
    if (empty($errors)) {
        $_SESSION['tasks'][$index] = [
            'task' => $task,
            'priority' => $priority,
            'deadline' => $deadline,
            'description' => $description
        ];

        // Set message sukses
        $_SESSION['message'] = 'Task berhasil diperbarui!';
        $_SESSION['type'] = 'success';
    } else {
        // Set message error
        $_SESSION['message'] = implode(' ', $errors);
        $_SESSION['type'] = 'danger';
    }
}

// Fungsi untuk menghapus task yang telah melewati deadline
function remove_past_tasks() {
    $today = date('Y-m-d');
    foreach ($_SESSION['tasks'] as $index => $task) {
        if (strtotime($task['deadline']) < strtotime($today)) {
            unset($_SESSION['tasks'][$index]);
        }
    }
    $_SESSION['tasks'] = array_values($_SESSION['tasks']); // Reset index array
}

// Jalankan fungsi untuk menghapus task yang telah lewat deadline setiap kali halaman dimuat
remove_past_tasks();

// Fungsi untuk menghapus task
if (isset($_POST['delete_task'])) {
    $index = $_POST['task_index'];
    unset($_SESSION['tasks'][$index]);
    $_SESSION['tasks'] = array_values($_SESSION['tasks']); // Reset index array

    // Set message sukses
    $_SESSION['message'] = 'Task berhasil dihapus!';
    $_SESSION['type'] = 'success';
}

// Fungsi untuk menghapus semua task
if (isset($_POST['delete_all'])) {
    session_destroy(); // Hapus semua data dalam session
    header("Location: ".$_SERVER['PHP_SELF']); // Redirect untuk memulai session baru
    exit;
}

// Fungsi sorting berdasarkan prioritas
function sort_by_priority($a, $b) {
    $priorities = ['Low' => 1, 'Medium' => 2, 'High' => 3];
    return $priorities[$b['priority']] <=> $priorities[$a['priority']];
}

// Fungsi sorting berdasarkan deadline
function sort_by_deadline($a, $b) {
    return strtotime($a['deadline']) <=> strtotime($b['deadline']);
}

// Terapkan filter jika ada permintaan
if (isset($_POST['apply_filter'])) {
    $sort_by = $_POST['sort_by'];
    
    if ($sort_by == 'priority') {
        usort($_SESSION['tasks'], 'sort_by_priority');
    } elseif ($sort_by == 'deadline') {
        usort($_SESSION['tasks'], 'sort_by_deadline');
    }
}
?>