<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class CVController extends Controller
{
    public function showForm()
    {
        $students = Student::orderBy('fullname')->get();
        $sampleLinks = [
            'Mẫu CV 1' => 'https://www.topcv.vn/mau-cv-tieng-viet/minimalism_v2',
            'Mẫu CV 2' => 'https://www.topcv.vn/mau-cv-tieng-viet/senior_v2',
            'Mẫu CV 3' => 'https://www.topcv.vn/mau-cv-tieng-viet/default_v2',
        ];
        return view('cv_form', compact('students', 'sampleLinks'));
    }

    public function getStudentName(Request $request)
    {
        $msv = $request->msv;
        $student = Student::where('msv', $msv)->first();
        return response()->json(['fullname' => $student?->fullname ?? '']);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'msv' => 'required|exists:students,msv',
            'cv_file' => 'required|mimes:pdf|max:2048'
        ]);

        $student = Student::where('msv', $request->msv)->first();
        $fullnameSlug = str_replace(' ', '-', remove_accents($student->fullname));
        $filename = "CV_{$fullnameSlug}_{$student->msv}.pdf";

        // Check tên file
        if ($request->file('cv_file')->getClientOriginalName() !== $filename) {
            return back()->withErrors(['cv_file' => "Tên file phải đúng: $filename"]);
        }

        // Lưu file vào storage/app/cv
        $request->file('cv_file')->storeAs('cv', $filename);

        $student->cv_uploaded = 1;
        $student->cv_filename = $filename;
        $student->save();

        return back()->with('success', 'Upload thành công!');
    }
}

// Helper: remove accents (bổ sung vào cùng file)
if (!function_exists('remove_accents')) {
    function remove_accents($str)
    {
        $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
        $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
        $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
        $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
        $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
        $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
        $str = preg_replace('/[đ]/u', 'd', $str);
        $str = preg_replace('/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u', 'A', $str);
        $str = preg_replace('/[ÈÉẸẺẼÊỀẾỆỂỄ]/u', 'E', $str);
        $str = preg_replace('/[ÌÍỊỈĨ]/u', 'I', $str);
        $str = preg_replace('/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u', 'O', $str);
        $str = preg_replace('/[ÙÚỤỦŨƯỪỨỰỬỮ]/u', 'U', $str);
        $str = preg_replace('/[ỲÝỴỶỸ]/u', 'Y', $str);
        $str = preg_replace('/[Đ]/u', 'D', $str);
        $str = preg_replace('/[ ]/u', '-', $str);
        return $str;
    }
}
