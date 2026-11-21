
<?php
/**
 * Joy of PHP
 * Uploads car images and saves their information in the database.
 */

require_once 'db.php';

// Get the VIN.
$vin = trim($_POST['VIN'] ?? '');

// Check that a VIN was provided.
if ($vin === '') {
    die("Please provide a VIN.");
}

// Check for upload errors.
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die("There was an error uploading the file.");
}

// Set the upload folder.
$uploadFolder = __DIR__ . '/uploads/';

// Create the folder if it does not exist.
if (!is_dir($uploadFolder)) {
    mkdir($uploadFolder, 0755, true);
}

// Check the uploaded file type.
$fileInfo = new finfo(FILEINFO_MIME_TYPE);
$fileType = $fileInfo->file($_FILES['file']['tmp_name']);

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];

if (!isset($allowedTypes[$fileType])) {
    die("Only JPG, PNG, GIF, and WEBP images are allowed.");
}

// Generate a unique filename.
$fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$fileType];

$targetPath = $uploadFolder . $fileName;
$imageName = 'uploads/' . $fileName;

// Move the uploaded file.
if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    die("Unable to save the uploaded image.");
}

// Insert the image information into the database.
$query = "INSERT INTO images (VIN, ImageFile) VALUES (?, ?)";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    unlink($targetPath);
    die("Database query failed.");
}

$stmt->bind_param("ss", $vin, $imageName);

if ($stmt->execute()) {
    echo "<h2>Image Uploaded Successfully</h2>";
    echo "<p>The image has been saved for VIN: "
        . htmlspecialchars($vin) . "</p>";

    echo "<img src='" . htmlspecialchars($imageName)
        . "' alt='Uploaded car image' width='150'>";

    echo "<p><a href='AddImage.php?VIN="
        . urlencode($vin) . "'>Add another image for this car</a></p>";
} else {
    unlink($targetPath);
    echo "<p>Error saving the image information: "
        . htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$mysqli->close();

include 'footer.php';
?>
