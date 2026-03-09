<?php

namespace App\Helpers;

class ResumeParser
{
    public static function parse($text)
    {
        $data = [];

        // Name (assuming first line is the name)
        $lines = explode("\n", $text);
        $data['name'] = trim($lines[0]);

        // Email
        preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}/', $text, $email);
        $data['email'] = $email[0] ?? null;

        // Skills (comma-separated)
        preg_match('/Skills?:\s*(.*)/i', $text, $skills);
        if (isset($skills[1])) {
            $data['skills'] = array_map('trim', explode(',', $skills[1]));
        } else {
            $data['skills'] = [];
        }

        // Education
        preg_match('/Education?:\s*(.*)/i', $text, $education);
        $data['education'] = $education[1] ?? null;

        // Experience
        preg_match('/Experience?:\s*(.*)/i', $text, $experience);
        $data['experience'] = $experience[1] ?? null;

        return $data;
    }
}