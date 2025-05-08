<?php
namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\DataBelumKerja;
use App\Models\DataKerja;
use App\Models\DataKuliah;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DataSiswaController extends Controller
{
    public function insertData(Request $request)
    {
        try {
            // Get the authenticated student
            $student = Students::where('user_id', Auth::id())->first();

            if (! $student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan.',
                ], 404);
            }

            // Based on status_setelah_lulus, validate and store data
            switch ($student->status_setelah_lulus) {
                case 'kuliah':
                    return $this->storeKuliahData($request, $student);

                case 'kerja':
                    return $this->storeKerjaData($request, $student);

                case 'belum_kerja':
                    return $this->storeBelumKerjaData($request, $student);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Status setelah lulus tidak valid.',
                    ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store kuliah data for student
     *
     * @param Request $request
     * @param Students $student
     * @return \Illuminate\Http\JsonResponse
     */
    private function storeKuliahData(Request $request, Students $student)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'nama_pt'           => 'required|string|max:255',
            'jurusan'           => 'required|string|max:255',
            'jenjang'           => 'required|string|in:D3,D4,S1,S2,S3',
            'tahun_masuk'       => 'required|integer|min:1900|max:2099',
            'status_beasiswa'   => 'required|string|in:ya,tidak',
            'nama_beasiswa'     => 'nullable|string|max:255',
            'prestasi_akademik' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Create or update kuliah data
        $dataKuliah = DataKuliah::updateOrCreate(
            ['student_id' => $student->id],
            [
                'nama_pt'           => $request->input('nama_pt'),
                'jurusan'           => $request->input('jurusan'),
                'jenjang'           => $request->input('jenjang'),
                'tahun_masuk'       => $request->input('tahun_masuk'),
                'status_beasiswa'   => $request->input('status_beasiswa'),
                'nama_beasiswa'     => $request->input('status_beasiswa') === 'ya' ? $request->input('nama_beasiswa') : null,
                'prestasi_akademik' => $request->input('prestasi_akademik'),
            ]
        );

        // Update student profile completion status
        $student->is_profile_complete = true;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Data kuliah berhasil disimpan',
            'data'    => $dataKuliah,
        ]);
    }

    /**
     * Store kerja data for student
     *
     * @param Request $request
     * @param Students $student
     * @return \Illuminate\Http\JsonResponse
     */
    private function storeKerjaData(Request $request, Students $student)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'nama_perusahaan'       => 'required|string|max:255',
            'posisi'                => 'required|string|max:255',
            'jenis_pekerjaan'       => 'required|string|max:255',
            'tanggal_mulai'         => 'required|date',
            'gaji'                  => 'nullable|integer',
            'sesuai_jurusan'        => 'required|string|in:ya,tidak',
            'kompetensi_dibutuhkan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Create or update kerja data
        $dataKerja = DataKerja::updateOrCreate(
            ['student_id' => $student->id],
            [
                'nama_perusahaan'       => $request->input('nama_perusahaan'),
                'posisi'                => $request->input('posisi'),
                'jenis_pekerjaan'       => $request->input('jenis_pekerjaan'),
                'tanggal_mulai'         => $request->input('tanggal_mulai'),
                'gaji'                  => $request->input('gaji'),
                'sesuai_jurusan'        => $request->input('sesuai_jurusan'),
                'kompetensi_dibutuhkan' => $request->input('kompetensi_dibutuhkan'),
            ]
        );

        // Update student profile completion status
        $student->is_profile_complete = true;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Data pekerjaan berhasil disimpan',
            'data'    => $dataKerja,
        ]);
    }

    /**
     * Store belum kerja data for student
     *
     * @param Request $request
     * @param Students $student
     * @return \Illuminate\Http\JsonResponse
     */
    private function storeBelumKerjaData(Request $request, Students $student)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'alasan'       => 'required|string',
            'keterampilan' => 'nullable|string',
            'minat'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Create or update belum kerja data
        $dataBelumKerja = DataBelumKerja::updateOrCreate(
            ['student_id' => $student->id],
            [
                'alasan'       => $request->input('alasan'),
                'keterampilan' => $request->input('keterampilan'),
                'minat'        => $request->input('minat'),
            ]
        );

        // Update student profile completion status
        $student->is_profile_complete = true;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data'    => $dataBelumKerja,
        ]);
    }
}
