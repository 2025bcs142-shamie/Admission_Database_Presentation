<?php

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query('SELECT program_id, program_name FROM Programs');
$programs = $stmt->fetchAll();
$error = isset($_GET['error']) ? $_GET['error'] : '';

require_once __DIR__ . '/../header.php';
?>
<h1>New Application</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="submit_application.php" method="POST" enctype="multipart/form-data">
    <label for="program_id">Program</label>
    <select id="program_id" name="program_id" required>
        <option value="">Select Program</option>
        <?php foreach ($programs as $program): ?>
            <option value="<?= htmlspecialchars($program['program_id']) ?>">
                <?= htmlspecialchars($program['program_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h2>Previous Qualifications</h2>

    <?php for ($i = 0; $i < 3; $i++): ?>
        <label for="school_name_<?= $i ?>">School Name</label>
        <input type="text" id="school_name_<?= $i ?>" name="school_name[]">

        <label for="grade_<?= $i ?>">Grade Average</label>
        <input type="number" id="grade_<?= $i ?>" name="grade[]" step="0.01" min="0">

        <label for="year_<?= $i ?>">Year Completed</label>
        <input type="number" id="year_<?= $i ?>" name="year[]" min="1900" max="2099">

        <label for="cert_type_<?= $i ?>">Certificate Type</label>
        <input type="text" id="cert_type_<?= $i ?>" name="cert_type[]">
    <?php endfor; ?>

    <label for="documents">Documents</label>
    <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" required>

    <button type="submit">Submit Application</button>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
