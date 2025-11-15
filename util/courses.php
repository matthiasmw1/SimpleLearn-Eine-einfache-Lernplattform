<?php
// util/courses.php

// Fake-Kurse – später kommen die aus der Datenbank
$courses = [
    [
        'id' => 1,
        'title' => 'Einführung in PHP',
        'description' => 'Lerne die Grundlagen von PHP für Webentwicklung.',
        'content' => 'Hier könnten später Lektionen, Dateien oder Videos zum Kurs "Einführung in PHP" stehen.',
        'owner_id' => 1, // gehört testUser
    ],
    [
        'id' => 2,
        'title' => 'HTML & CSS Basics',
        'description' => 'Erstelle schöne Webseiten mit HTML und CSS.',
        'content' => 'Grundlagen von HTML, CSS, Layouts und responsivem Design.',
        'owner_id' => 1, // gehört testUser
    ],
    [
        'id' => 3,
        'title' => 'Git und Versionierung',
        'description' => 'Arbeite im Team mit Git und GitHub.',
        'content' => 'Einführung in Git, Commits, Branches und Zusammenarbeit mit GitHub.',
        'owner_id' => 2, // gehört "jemand anderem"
    ],
];

function getAllCourses(): array {
    global $courses;
    return $courses;
}

function findCourseById(int $id): ?array {
    global $courses;
    foreach ($courses as $c) {
        if ((int)$c['id'] === $id) {
            return $c;
        }
    }
    return null;
}

function getCoursesByOwner(int $ownerId): array {
    global $courses;
    $result = [];
    foreach ($courses as $c) {
        if ((int)$c['owner_id'] === $ownerId) {
            $result[] = $c;
        }
    }
    return $result;
}
