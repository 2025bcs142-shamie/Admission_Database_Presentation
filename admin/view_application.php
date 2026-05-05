<?php

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$applicationId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($applicationId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';

$stmt = $pdo->prepare(
    'SELECT a.*, ap.*, p.program_name
     FROM Applications a
     JOIN Applicants ap ON a.applicant_id = ap.applicant_id
     JOIN Programs p ON a.program_id = p.program_id
     WHERE a.application_id = ?'
);
$stmt->execute([$applicationId]);
$application = $stmt->fetch();

if (!$application) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM Qualifications WHERE applicant_id = ?');
$stmt->execute([$application['applicant_id']]);
$qualifications = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM Documents WHERE application_id = ?');
$stmt->execute([$applicationId]);
$documents = $stmt->fetchAll();

require_once __DIR__ . '/../header.php';
?>
<h1>Application Review</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<h2>Applicant Details</h2>
<p><strong>Name:</strong> <?= htmlspecialchars($application['first_name'] . ' ' . $application['last_name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
<p><strong>Date of Birth:</strong> <?= htmlspecialchars($application['date_of_birth']) ?></p>
<p><strong>Gender:</strong> <?= htmlspecialchars($application['gender']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($application['phone']) ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($application['address'] ?? '') ?></p>
<p><strong>Nationality:</strong> <?= htmlspecialchars($application['nationality'] ?? '') ?></p>

<h2>Application Details</h2>
<p><strong>Program:</strong> <?= htmlspecialchars($application['program_name']) ?></p>
<p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p>
<p><strong>Application Date:</strong> <?= htmlspecialchars($application['application_date']) ?></p>

<h2>Qualifications</h2>
<?php if (empty($qualifications)): ?>
    <p>No qualifications submitted.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>School Name</th>
                <th>Grade Average</th>
                <th>Year Completed</th>
                <th>Certificate Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qualifications as $qualification): ?>
                <tr>
                    <td><?= htmlspecialchars($qualification['school_name']) ?></td>
                    <td><?= htmlspecialchars($qualification['grade_average']) ?></td>
                    <td><?= htmlspecialchars($qualification['year_completed']) ?></td>
                    <td><?= htmlspecialchars($qualification['certificate_type']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Documents</h2>
<?php if (empty($documents)): ?>
    <p>No documents uploaded.</p>
<?php else: ?>
    <ul>
        <?php foreach ($documents as $doc): ?>
            <li>
                <a href="<?= BASE_URL ?>uploads/<?= htmlspecialchars($doc['file_name']) ?>" target="_blank">View Document</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($application['status'] === 'Pending'): ?>
    <h2>Review Application</h2>
    <form action="review_application.php" method="POST">
        <input type="hidden" name="application_id" value="<?= htmlspecialchars($application['application_id']) ?>">

        <label>
            <input type="radio" name="decision" value="Approved" required>
            Approve
        </label>

        <label>
            <input type="radio" name="decision" value="Rejected" required>
            Reject
        </label>

        <label for="remarks">Remarks</label>
        <textarea id="remarks" name="remarks"></textarea>

        <button type="submit">Submit Review</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action="review_application.php"]');
            const remarks = document.getElementById('remarks');
            const radios = document.querySelectorAll('input[name="decision"]');

            function toggleRemarksRequired() {
                const selected = document.querySelector('input[name="decision"]:checked');
                remarks.required = selected && selected.value === 'Rejected';
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', toggleRemarksRequired);
            });

            if (form) {
                form.addEventListener('submit', toggleRemarksRequired);
            }
        });
    </script>
<?php else: ?>
    <h2>Review Outcome</h2>
    <p><strong>Decision:</strong> <?= htmlspecialchars($application['status']) ?></p>
    <p><strong>Remarks:</strong> <?= htmlspecialchars($application['admin_remarks'] ?? '') ?></p>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>
