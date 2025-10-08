@extends('layouts.main')

@section('title', 'Dashboard AI Agent')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <!--begin::Page title-->
    <div class="d-flex flex-column justify-content-center">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Dashboard AI Agent</h1>
        <!--end::Title-->
        <!--begin::Breadcrumb-->
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">/</li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">Dashboard AI Agent</li>
            <!--end::Item-->
        </ul>
        <!--end::Breadcrumb-->
    </div>
    <!--end::Page title-->
    {{-- <div class="d-none d-md-block h-40px border-start border-gray-200 mx-10"></div> --}}
    <div class="d-flex gap-3 gap-lg-8 flex-wrap">
    </div>
</div>

<!--begin::Actions-->

<!--end::Actions-->
@stop 

@section('content')
<div class="row g-5">
    <div class="col-md-12">
        @if (session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-success me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"/>
                </svg>
            </span>
            <div class="d-flex flex-column">
                <h6 class="mb-1 text-success">{{ session('success') }}</h6>
            </div>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-danger me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                <rect x="9" y="13.0283" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(-45 9 13.0283)" fill="currentColor"/>
                <rect x="9.86664" y="7.93359" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(45 9.86664 7.93359)" fill="currentColor"/>
                </svg>
            </span>
            <div class="d-flex flex-column">
                <h6 class="mb-1 text-danger">{{ session('error') }}</h6>
            </div>
        </div>
        @endif
    </div>
    <div class="col-lg-12">
        <div class="card card-flush shadow-sm p-0" id="manage_table">
            @csrf
            <div class="card-header py-0">
                <h3 class="card-title fs-1 fw-bolder py-0"></h3>
                <div class="card-toolbar">
                </div>
            </div>
            <div class="card-body pt-0 pb-5">
              <div class="row">
                <div class="col-lg-6 offset-lg-3">
                  <div class="d-flex flex-column">
                    <div class="fs-1 fw-bold text-center mb-5">Tanyakan apapun sama saya:</div>
                    <div class="d-flex flex-row">
                      <input id="q" class="d-flex form-control flex-column-fluid me-5" placeholder="Pertanyaan..." style="width:60%">
                      <button class="d-flex btn btn-primary mt-2" id="go">
                          <span class="spinner-border spinner-border-sm me-2 d-none" id="goSpinner" role="status" aria-hidden="true"></span>
                          <span id="goText">Kirim</span>
                        </button>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="d-flex flex-column mb-5">
                    <div class="fs-2 fw-bold mb-3">Planned SQL</div>
                    <pre id="sql" class="output sql w-100 bg-gray-100 p-5 rounded"></pre>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="d-flex flex-row fs-2 fw-bold mb-3">
                    <div class="d-flex align-items-center gap-2">
                      Answer
                      <span id="streamBadge" class="badge bg-secondary d-none">Streaming…</span>
                    </div>
                  </div>
                  <pre id="out" class="output w-100 bg-gray-100 p-5 rounded"></pre>
                </div>
              </div>
            </div>
            <div class="card-footer text-end">
            </div>
        </div>
    </div>
</div>
@stop

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css"/>
<style>
    .hidden {display:none;}
    tr th {padding: 10px !important;}
    tr td {padding: 1px 1px 1px 5px !important; font-size: 12px !important; vertical-align: middle !important;}
    .table-striped>tbody>tr:nth-of-type(odd)>* { --bs-table-accent-bg: #eeeeee !important;}
    .is-loading { cursor: progress; }
    .output {
      white-space: pre-wrap;      /* bungkus baris tapi tetap hormati newline */
      word-wrap: break-word;      /* pecah kata panjang */
      overflow-x: hidden;         /* jangan pakai scroll horizontal */
      overflow-y: auto;           /* biar kalau panjang ke bawah tetap scroll vertikal */
      max-height: 400px;          /* opsional: batasi tinggi area */
    }
</style>
@stop

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script>
(() => {
  const $q = document.getElementById('q');
  const $go = document.getElementById('go');
  const $goSpinner = document.getElementById('goSpinner');
  const $goText = document.getElementById('goText');
  const $sql = document.getElementById('sql');
  const $out = document.getElementById('out');
  const $badge = document.getElementById('streamBadge');
  const token = document.querySelector('input[name="_token"]')?.value || '';

  let ctrl; // AbortController untuk memutus stream sebelumnya
  let streaming = false;

  function appendOut(text) {
    $out.textContent += text;
    $out.scrollTop = $out.scrollHeight;
  }

  function setError(msg) {
    appendOut(`\n[Error] ${msg}`);
  }

  function setLoading(on) {
    streaming = !!on;
    // tombol & input
    $go.disabled = on;
    $q.disabled = on;
    // spinner & teks
    $goSpinner.classList.toggle('d-none', !on);
    $goText.textContent = on ? 'Mengirim…' : 'Kirim';
    // badge “Streaming…”
    if ($badge) $badge.classList.toggle('d-none', !on);
    // body cursor
    document.body.classList.toggle('is-loading', on);
  }

  function parsePossibleJson(line) {
    if (line.startsWith('data:')) {
      const payload = line.slice(5).trim();
      if (payload === '[DONE]') return { done: true };
      try { return JSON.parse(payload); } catch { return null; }
    }
    try { return JSON.parse(line); } catch { return null; }
  }

  async function streamOnce(question) {
    // Putuskan stream sebelumnya (kalau ada)
    if (ctrl) ctrl.abort();
    ctrl = new AbortController();

    $sql.textContent = '';
    $out.textContent = '';
    setLoading(true);

    let resp;
    try {
      resp = await fetch(`{{ url('ai/stream') }}`, {
        method: 'POST',
        signal: ctrl.signal,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'text/event-stream, application/x-ndjson, application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          _token: token,
          question,
          stream: true
        })
      });
    } catch (e) {
      setError(`Network error: ${e?.message || e}`);
      setLoading(false);
      return;
    }

    if (!resp.ok) {
      setError(`HTTP ${resp.status}`);
      setLoading(false);
      return;
    }
    if (!resp.body) {
      setError('Empty body');
      setLoading(false);
      return;
    }

    const reader = resp.body.getReader();
    const decoder = new TextDecoder();
    let buf = '';

    try {
      while (true) {
        const { value, done } = await reader.read();
        if (done) break;

        buf += decoder.decode(value, { stream: true });

        let nl;
        while ((nl = buf.search(/\r?\n/)) >= 0) {
          const raw = buf.slice(0, nl);
          buf = buf.slice(nl + (buf[nl] === '\r' && buf[nl+1] === '\n' ? 2 : 1));
          const line = raw.trim();
          if (!line) continue;

          const obj = parsePossibleJson(line);
          if (!obj) continue;

          if (obj.phase === 'plan') {
            if (obj.sql) $sql.textContent = obj.sql;
            if (obj.error) setError('Plan error: ' + obj.error);
            continue;
          }

          if (typeof obj.response === 'string') {
            appendOut(obj.response);
          }

          if (obj.choices && obj.choices[0]) {
            const delta = obj.choices[0].delta?.content ?? obj.choices[0].message?.content ?? '';
            if (delta) appendOut(delta);
          }

          if (obj.error) setError(obj.error);
          if (obj.done === true) {
            setLoading(false);
            return;
          }
        }
      }

      if (buf.trim()) {
        const tail = parsePossibleJson(buf.trim());
        if (tail) {
          if (tail.phase === 'plan' && tail.sql) $sql.textContent = tail.sql;
          const finalText =
            tail.response ??
            tail.choices?.[0]?.message?.content ??
            tail.choices?.[0]?.text ??
            '';
          if (finalText) appendOut(finalText);
          if (tail.error) setError(tail.error);
        }
      }
    } catch (e) {
      if (e.name !== 'AbortError') setError(`Stream error: ${e?.message || e}`);
    } finally {
      try { reader.releaseLock(); } catch {}
      setLoading(false);
    }
  }

  $go.onclick = () => {
    const question = ($q.value || '').trim();
    if (!question) return;
    streamOnce(question);
  };

  $q.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      $go.click();
    }
  });
})();
</script>
@stop 
