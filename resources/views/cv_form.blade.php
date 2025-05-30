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
                        <!-- Chú ý quan trọng -->
                        <div class="alert alert-warning text-center fw-bold fs-5"
                            style="border-width:2px; border-left:5px solid #ffc107;">
                            <span style="color:#b35b00;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#ffc107"
                                    class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                    <path
                                        d="M7.938 2.016a.13.13 0 0 1 .125 0l6.857 11.856c.027.047.04.1.04.154a.267.267 0 0 1-.267.267H1.307a.267.267 0 0 1-.267-.267.25.25 0 0 1 .04-.154L7.938 2.016zM8 4.58 2.15 14h11.7L8 4.58zM7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm.93-4.481-.082 2.5a.552.552 0 0 0 1.104 0l-.082-2.5a.552.552 0 0 0-1.104 0z" />
                                </svg>
                                <br>
                                <span class="d-block mt-1">
                                    <span style="color:#d2691e;">**Lưu ý quan trọng:**</span>
                                    <br>
                                    Sinh viên <span style="color:#dc3545; font-weight:bold;">phải hoàn thành</span>
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLScXSjfQLmnEEWZCsjJDYT3glmt6RAX12N6bY_npaO7ho9uQiw/viewform"
                                        target="_blank"
                                        style="color:#007bff; text-decoration:underline; font-weight:bold;">
                                        form đăng ký thông tin tại đây
                                    </a>
                                    <span style="color:#dc3545;">trước khi nộp CV!</span>
                                </span>
                            </span>
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
                            <!-- Checkbox xác nhận làm Google Form -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="form_done" name="form_done"
                                        value="1" required>
                                    <label class="form-check-label fw-bold text-success" for="form_done">
                                        Tôi cam kết đã hoàn thành
                                        <a href="https://docs.google.com/forms/d/e/1FAIpQLScXSjfQLmnEEWZCsjJDYT3glmt6RAX12N6bY_npaO7ho9uQiw/viewform"
                                            target="_blank" class="text-primary text-decoration-underline">
                                            form đăng ký thông tin
                                        </a>
                                    </label>
                                </div>
                            </div>
                            <!-- Trạng thái đã upload CV chưa -->
                            <div class="mb-3">
                                <label class="form-label">Trạng thái nộp CV:</label>
                                <div id="upload-status" class="fw-bold"></div>
                            </div>
                            <!-- Trạng thái đã làm Google Form chưa -->
                            <div class="mb-3">
                                <label class="form-label">Trạng thái làm Google Form:</label>
                                <div id="form-done-status" class="fw-bold"></div>
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
        // An toàn cho mọi môi trường
        let ajaxUrl = '/get-student-name';
        // hoặc nếu bắt buộc phải có domain:
        let ajaxUrl2 = window.location.protocol + '//' + window.location.host + '/get-student-name';

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
                    url: ajaxUrl2,
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
                        // Trạng thái upload CV
                        if (res.cv_uploaded && res.cv_filename) {
                            let fileUrl = '/storage/cv/' + res.cv_filename;
                            $('#upload-status').html(
                                '<span class="text-success"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 1 1 11 0 5.5 5.5 0 0 1-11 0zm13 0A7.5 7.5 0 1 0 1 8a7.5 7.5 0 0 0 14.5 0z"/><path d="M10.97 5.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L5.324 9.384a.75.75 0 1 1 1.06-1.06l1.094 1.093 3.492-4.438z"/></svg> Đã nộp CV: <a href="' +
                                fileUrl + '" target="_blank">' + res.cv_filename + '</a></span>'
                            );
                        } else {
                            $('#upload-status').html(
                                '<span class="text-danger"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .236-.112.262-.25.04-.232.166-.516.465-.516.243 0 .488.176.488.483 0 .253-.158.393-.457.623-.369.287-.637.641-.637 1.181v.07c0 .13.105.235.235.235h.819a.235.235 0 0 0 .235-.234c0-.393.247-.623.571-.877.335-.262.647-.587.647-1.175C8.5 5.137 7.735 4.5 6.885 4.5c-.968 0-1.617.737-1.63 1.286zM8 13a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg> Chưa nộp CV</span>'
                            );
                        }
                        // Trạng thái làm Google Form
                        if (res.form_done) {
                            $('#form-done-status').html(
                                '<span class="text-success"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 1 1 11 0 5.5 5.5 0 0 1-11 0zm13 0A7.5 7.5 0 1 0 1 8a7.5 7.5 0 0 0 14.5 0z"/><path d="M10.97 5.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L5.324 9.384a.75.75 0 1 1 1.06-1.06l1.094 1.093 3.492-4.438z"/></svg> Đã xác nhận đã làm form</span>'
                            );
                        } else {
                            $('#form-done-status').html(
                                '<span class="text-warning"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/><path d="M7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm.93-4.481-.082 2.5a.552.552 0 0 0 1.104 0l-.082-2.5a.552.552 0 0 0-1.104 0z"/></svg> Chưa xác nhận làm form</span>'
                            );
                        }
                    }
                });
            } else {
                $('#fullname').val('');
                $('#cv-file-name').val('');
                $('#upload-status').html('');
                $('#form-done-status').html('');
            }
        });

        // Copy tên file
        $('#copy-cv-file-name').on('click', function() {
            let filename = $('#cv-file-name').val();
            if (filename) {
                navigator.clipboard.writeText(filename);
                $(this).text('Đã copy!').removeClass('btn-outline-secondary').addClass('btn-success');
                setTimeout(() => {
                    $(this).text('Copy tên file').removeClass('btn-success').addClass(
                        'btn-outline-secondary');
                }, 1500);
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
