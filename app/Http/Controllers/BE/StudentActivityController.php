<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentActivityController extends Controller
{
    /**
     * Get student activities - limited to 4
     */
    public function getActivities(Request $request)
    {
        try {
            // Get current student
            $user = Auth::user();
            $student = $user->student ?? null;
            
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student profile not found'
                ], 404);
            }
            
            // Get filter parameter
            $filter = $request->input('filter', 'all');
            
            // Generate activities - limited to exactly 4 for all responses
            $activities = $this->gatherActivities($student, $filter);
            
            // Always return exactly 4 activities
            return response()->json([
                'status' => 'success',
                'data' => array_slice($activities, 0, 4)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve activities: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Gather activities based on student status
     */
    private function gatherActivities($student, $filter)
    {
        $activities = [];
        
        // Add login activity
        if ($filter === 'all' || $filter === 'login') {
            $activities[] = [
                'id' => 'login-recent',
                'title' => 'Login Terakhir',
                'description' => 'Anda login pada ' . now()->toDateTimeString(),
                'timestamp' => now()->toIso8601String(),
                'type' => 'login',
                'icon' => 'login-variant'
            ];
        }
        
        // Add profile activity
        if ($filter === 'all' || $filter === 'profile') {
            $activities[] = [
                'id' => 'profile-update',
                'title' => 'Profil Diperbarui',
                'description' => 'Profil anda telah diperbarui',
                'timestamp' => now()->subDays(2)->toIso8601String(),
                'type' => 'profile',
                'icon' => 'account-edit'
            ];
        }
        
        // Add status-specific activities - limited to just 1-2 per status
        if ($student->status_setelah_lulus === 'belum_kerja') {
            // Questionnaire activity for "belum_kerja" status
            if ($filter === 'all' || $filter === 'questionnaire') {
                $activities[] = [
                    'id' => 'questionnaire-reminder',
                    'title' => 'Pengingat Kuesioner',
                    'description' => 'Silakan isi kuesioner untuk mendapatkan rekomendasi karir',
                    'timestamp' => now()->subDay()->toIso8601String(),
                    'type' => 'questionnaire',
                    'icon' => 'clipboard-list'
                ];
                
                // Only add recommendation if completed questionnaire
                if ($student->has_completed_questionnaire) {
                    $activities[] = [
                        'id' => 'recommendation-available',
                        'title' => 'Rekomendasi Tersedia',
                        'description' => 'Rekomendasi pekerjaan yang sesuai dengan anda telah tersedia',
                        'timestamp' => now()->subDays(3)->toIso8601String(),
                        'type' => 'questionnaire',
                        'icon' => 'lightbulb'
                    ];
                }
            }
        } elseif ($student->status_setelah_lulus === 'kerja') {
            // Add job-specific activities - limited to 2
            if ($filter === 'all' || $filter === 'profile') {
                $activities[] = [
                    'id' => 'job-update',
                    'title' => 'Informasi Pekerjaan',
                    'description' => 'Informasi pekerjaan anda telah diperbarui',
                    'timestamp' => now()->subDays(1)->toIso8601String(),
                    'type' => 'profile',
                    'icon' => 'briefcase'
                ];
                
                $activities[] = [
                    'id' => 'salary-update',
                    'title' => 'Informasi Gaji',
                    'description' => 'Data gaji anda telah diperbarui',
                    'timestamp' => now()->subDays(3)->toIso8601String(),
                    'type' => 'profile',
                    'icon' => 'cash-multiple'
                ];
            }
        } elseif ($student->status_setelah_lulus === 'kuliah') {
            // Add education-specific activities - limited to 2
            if ($filter === 'all' || $filter === 'profile') {
                $activities[] = [
                    'id' => 'education-update',
                    'title' => 'Informasi Pendidikan',
                    'description' => 'Informasi pendidikan anda telah diperbarui',
                    'timestamp' => now()->subDays(1)->toIso8601String(),
                    'type' => 'profile',
                    'icon' => 'school'
                ];
                
                $activities[] = [
                    'id' => 'university-update',
                    'title' => 'Data Perguruan Tinggi',
                    'description' => 'Informasi perguruan tinggi telah diperbarui',
                    'timestamp' => now()->subDays(3)->toIso8601String(),
                    'type' => 'profile',
                    'icon' => 'school'
                ];
            }
        }
        
        // Sort by timestamp (newest first)
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        // Return only up to 4 activities regardless of filters
        return array_slice($activities, 0, 4);
    }
}
