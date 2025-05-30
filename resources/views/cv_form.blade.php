<!-- resources/views/cv_form.blade.php -->
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Upload CV sinh viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-7 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">Nộp CV Sinh viên lớp SE06303</h2>
                        <div class="mb-4 text-center">
                            <div id="countdown-box" class="fw-bold fs-5 text-primary"></div>
                            <div id="extension-note" class="text-danger mt-1" style="display:none;"></div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success text-center">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('cv.upload') }}" enctype="multipart/form-data"
                            autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <label for="msv-select" class="form-label">Mã sinh viên (MSV)</label>
                                <select id="msv-select" name="msv" class="form-select" required>
                                    <option value="">-- Chọn MSV --</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->msv }}">{{ $student->msv }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" id="fullname" class="form-control" readonly
                                    placeholder="Họ và tên sẽ tự động hiển thị">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tên file cần đặt:</label>
                                <input type="text" id="cv-file-name" class="form-control bg-light" readonly
                                    style="font-weight:bold; color: #198754;">
                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2"
                                    id="copy-cv-file-name">
                                    Copy tên file
                                </button>
                                <div class="form-text">
                                    Copy đúng tên này cho file CV trước khi upload.<br>
                                    Chỉ nhận file PDF và đúng tên này!
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cv_file" class="form-label">Chọn file CV (PDF)</label>
                                <input type="file" name="cv_file" class="form-control" id="cv_file"
                                    accept="application/pdf" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Nộp CV</button>
                        </form>

                        <hr class="my-4">
                        <h5 class="mb-2 text-center">Tham khảo mẫu CV:</h5>
                        <ul class="list-group list-group-flush mb-0">
                            @foreach ($sampleLinks as $label => $url)
                                <li class="list-group-item">
                                    <a href="{{ $url }}" target="_blank"
                                        class="text-decoration-none">{{ $label }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <footer class="text-center mt-3 text-muted small">© {{ date('Y') }} Nộp CV - LinhHN13 - SE06303
                </footer>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeAccents(str) {
            return str
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .replace(/\s+/g, '-');
        }

        $('#msv-select').on('change', function() {
            let msv = $(this).val();
            if (msv) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('cv.getStudentName') }}',
                    data: {
                        msv: msv,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#fullname').val(res.fullname);
                        let nameSlug = removeAccents(res.fullname || '').replace(/-+/g, '-');
                        if (nameSlug && msv) {
                            $('#cv-file-name').val(`CV_${nameSlug}_${msv}.pdf`);
                        } else {
                            $('#cv-file-name').val('');
                        }
                    }
                });
            } else {
                $('#fullname').val('');
                $('#cv-file-name').val('');
            }
        });

        $('form').on('submit', function() {
            $('button[type=submit]').prop('disabled', true)
                .html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang upload...'
                );
        });

        // ----------- Countdown + Lock Form Logic ------------

        let deadline = new Date("2025-05-30T14:30:00+07:00").getTime();
        let reopenTime = deadline + 60 * 60 * 1000; // +1 hour
        let endExtension = reopenTime + 30 * 60 * 1000; // +30 mins
        let now = null;
        let interval = setInterval(function() {
            now = new Date().getTime();

            let distMain = deadline - now;
            let distReopen = reopenTime - now;
            let distFinal = endExtension - now;

            if (distMain > 0) {
                // Đang trong thời gian nộp bình thường
                showCountdown(distMain, "Thời gian còn lại:");
                enableForm();
                $("#extension-note").hide();
            } else if (distReopen > 0) {
                // Đã hết hạn chính, chờ mở lại 30 phút
                $("#countdown-box").text("Hết hạn nộp! Hệ thống sẽ mở bổ sung trong " + secondsToHMS(distReopen));
                disableForm();
                $("#extension-note").hide();
            } else if (distFinal > 0) {
                // Đang mở bổ sung 30 phút
                showCountdown(distFinal, "Mở bổ sung 30 phút cuối cùng! Còn lại:");
                enableForm();
                $("#extension-note").show().text("Đang mở bổ sung 30 phút cuối cùng!");
            } else {
                // Hết hoàn toàn
                $("#countdown-box").text("Đã kết thúc nộp CV!");
                disableForm();
                $("#extension-note").hide();
                clearInterval(interval);
            }
        }, 1000);

        function showCountdown(dist, label) {
            let t = secondsToHMS(dist);
            $("#countdown-box").text(`${label} ${t}`);
        }

        function secondsToHMS(ms) {
            let total = Math.floor(ms / 1000);
            let h = Math.floor(total / 3600);
            let m = Math.floor((total % 3600) / 60);
            let s = total % 60;
            return [h, m, s].map(x => x.toString().padStart(2, "0")).join(":");
        }

        function disableForm() {
            $('form input, form select, form button').prop('disabled', true);
            $('button[type=submit]').addClass('btn-secondary').removeClass('btn-primary');
        }

        function enableForm() {
            $('form input, form select, form button').prop('disabled', false);
            $('button[type=submit]').addClass('btn-primary').removeClass('btn-secondary');
        }
    </script>

</body>

</html>
