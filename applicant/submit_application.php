<?php

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['program_id'])) {
    header('Location: apply.php?error=' . urlencode('Invalid application request'));
    exit;
}

$applicantId = $_SESSION['user_id'];
$programId = (int) $_POST['program_id'];
$uploadDir = __DIR__ . '/../uploads/';
$allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];
$maxFileSize = 5 * 1024 * 1024;

if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
    header('Location: apply.php?error=' . urlencode('Upload directory is not available'));
    exit;
}

if (
    !isset($_FILES['documents']) ||
    !is_array($_FILES['documents']['name']) ||
    count(array_filter($_FILES['documents']['name'])) === 0
) {
    header('Location: apply.php?error=' . urlencode('Please upload at least one document'));
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO Applications (applicant_id, program_id, application_date) VALUES (?, ?, CURDATE())'
    );
    $stmt->execute([$applicantId, $programId]);
    $applicationId = $pdo->lastInsertId();

    $schoolNames = $_POST['school_name'] ?? [];
    $grades = $_POST['grade'] ?? [];
    $years = $_POST['year'] ?? [];
    $certTypes = $_POST['cert_type'] ?? [];

    if (!empty(trim($schoolNames[0] ?? ''))) {
        $qualificationStmt = $pdo->prepare(
            'INSERT INTO Qualifications (applicant_id, school_name, grade_average, year_completed, certificate_type)
             VALUES (?, ?, ?, ?, ?)'
        );

        foreach ($schoolNames as $index => $schoolName) {
            $schoolName = trim($schoolName);
            $grade = trim((string) ($grades[$index] ?? ''));
            $year = trim((string) ($years[$index] ?? ''));
            $certType = trim($certTypes[$index] ?? '');

            if ($schoolName === '') {
                continue;
            }

            $qualificationStmt->execute([
                $applicantId,
                $schoolName,
                $grade,
                $year,
                $certType,
            ]);
        }
    }

    $documentStmt = $pdo->prepare(
        'INSERT INTO Documents (applicant_id, application_id, document_type, file_name) VALUES (?, ?, ?, ?)'
    );

    $fileCount = count($_FILES['documents']['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        $fileError = $_FILES['documents']['error'][$i];
        $originalName = $_FILES['documents']['name'][$i];
        $tmpName = $_FILES['documents']['tmp_name'][$i];
        $fileSize = $_FILES['documents']['size'][$i];

        if ($originalName === '') {
            continue;
        }

        if ($fileError !== UPLOAD_ERR_OK) {
            throw new RuntimeException('One of the uploaded files is invalid');
        }

        if ($fileSize > $maxFileSize) {
            throw new RuntimeException('Each file must be 5 MB or smaller');
        }

        $mimeType = mime_content_type($tmpName);
        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            throw new RuntimeException('Only PDF, JPG, and PNG files are allowed');
        }

        $uniqueName = uniqid() . '_' . basename($originalName);
        $destination = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new RuntimeException('Failed to upload supporting document');
        }

        $documentStmt->execute([
            $applicantId,
            $applicationId,
            'Supporting Document',
            $uniqueName,
        ]);
    }

    $pdo->commit();
    header('Location: dashboard.php?success=1');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header('Location: apply.php?error=' . urlencode($e->getMessage()));
    exit;
}
