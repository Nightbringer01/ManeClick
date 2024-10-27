<?php
header('Content-Type: application/json');
include '../config/db.php';

// Retrieve POST data
$isGoalReached = $_POST['isGoalReached'];
$goal = $_POST['goal'];
$patient_id = $_POST['patient_id'];

// Validate and process data
if (!isset($isGoalReached) || !isset($goal) || !isset($patient_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit;
}

// Insert or update the goal and its confirmation
// Example query; adjust based on your database schema
$sql = "INSERT INTO sesh_goals (patient_id, goal, is_goal_reached) VALUES (:patient_id, :goal, :is_goal_reached) ON DUPLICATE KEY UPDATE goal = VALUES(goal), is_goal_reached = VALUES(is_goal_reached)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':patient_id', $patient_id);
$stmt->bindParam(':goal', $goal);
$stmt->bindParam(':is_goal_reached', $isGoalReached);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Goal confirmation recorded']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to record goal confirmation']);
}
?>
