<?php include "function.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="assets/Favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/style.css" />
    <title>Manajemen Task : ToDoList</title>
    
</head>
<body>
    <?php if (isset($_SESSION['message'])) : ?> 
        <div class="alert alert-<?= $_SESSION['type']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
            unset($_SESSION['message']);
            unset($_SESSION['type']);
        ?>
    <?php endif; ?>

    <h1>Manajemen Task</h1>
    <div class="container">

        <div class="left">
            <div class="calendar" style="margin-bottom: 0.5rem;">
                <!-- Kalender untuk menampilkan task -->
                <div id="calendar" style="margin: 0.5rem;"></div>

                <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var calendarEl = document.getElementById('calendar');

                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            events: [
                                <?php if (!empty($_SESSION['tasks'])): ?>
                                    <?php foreach ($_SESSION['tasks'] as $task): ?>
                                        {
                                            title: '<?= htmlspecialchars($task['task']) ?>',
                                            start: '<?= htmlspecialchars($task['deadline']) ?>',
                                            description: '<?= htmlspecialchars($task['description']) ?>',
                                            extendedProps: {
                                                priority: '<?= htmlspecialchars($task['priority']) ?>'
                                            }
                                        },
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            ],
                            eventClick: function (info) {
                                alert(
                                    'Task: ' + info.event.title +
                                    '\nDescription: ' + info.event.extendedProps.description +
                                    '\nPriority: ' + info.event.extendedProps.priority
                                );
                            }
                        });

                        calendar.render();
                    });

                    // Fungsi untuk mereset form modal saat menambah task baru
                    function resetForm() {
                        document.getElementById('taskForm').reset();
                        document.getElementById('taskModalLabel').textContent = 'Add Task';
                        document.getElementById('taskButton').textContent = 'Add Task';
                        document.getElementById('taskButton').name = 'add_task';
                        document.getElementById('task_index').value = '';
                    }

                    // Fungsi untuk mengisi form modal saat mengedit task
                    function editTask(index) {
                        var tasks = <?= json_encode($_SESSION['tasks']) ?>;
                        document.getElementById('task').value = tasks[index].task;
                        document.getElementById('priority').value = tasks[index].priority;
                        document.getElementById('deadline').value = tasks[index].deadline;
                        document.getElementById('description').value = tasks[index].description;

                        document.getElementById('taskModalLabel').textContent = 'Edit Task';
                        document.getElementById('taskButton').textContent = 'Save Changes';
                        document.getElementById('taskButton').name = 'edit_task';
                        document.getElementById('task_index').value = index;
                    }
                </script>

                <!-- Modal Tambah/Edit Task -->
                <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="taskModalLabel">Add Task</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="taskForm" method="POST" action="">
                                    <input type="hidden" id="task_index" name="task_index">
                                    <div class="mb-3">
                                        <label for="task" class="form-label">Task:</label>
                                        <input type="text" id="task" name="task" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority:</label>
                                        <select id="priority" name="priority" class="form-select" required>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="deadline" class="form-label">Deadline:</label>
                                        <input type="date" id="deadline" name="deadline" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description:</label>
                                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-success" type="submit" id="taskButton" name="add_task">
                                      <i class="fas fa-plus"></i>Add Task
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="right">
            <div class="today-date">
                <form method="POST" action="">
                  <div class="mb-1">
                    <label for="sort_by" class="form-label">Sort by:</label>
                    <select id="sort_by" name="sort_by" class="form-select">
                      <option value="priority">Priority</option>
                      <option value="deadline">Deadline</option>
                    </select>
                  </div>
                  <button class="btn btn-success" type="submit" name="apply_filter" >
                      <i class="fa-solid fa-arrow-up-short-wide"></i>Short
                  </button>
                </form>
              
              <button class="add-event" type="button" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="resetForm()">
                <i class="fas fa-plus"></i>
              </button>
                <form method="POST" action="">
                  <div class="mb-3">
                      <button type="submit" name="delete_all" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete all tasks?')">
                          <i class="fa-regular fa-trash-can"></i> Delete All Tasks
                      </button>
                  </div>
              </form>
            </div>
            
            <!-- Daftar Task -->
            <div class="events">
                <h3>Daftar Task</h3>
                <?php if (!empty($_SESSION['tasks'])): ?>
                    <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
                        <div class="task-item">
                            <strong><?= htmlspecialchars($task['task']) ?></strong>
                            (Priority: <?= htmlspecialchars($task['priority']) ?>, Deadline: <?= htmlspecialchars($task['deadline']) ?>)
                            <p><em><?= htmlspecialchars($task['description']) ?></em></p>
                            <div class="mt-2">
                                <!-- Tombol untuk menampilkan modal edit task -->
                                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="editTask(<?= $index ?>)">Edit</button>

                                <!-- Form untuk menghapus task -->
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="task_index" value="<?= $index ?>">
                                    <button type="submit" name="delete_task" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada task.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


