@stack('js')
<script src="{{ mix('assets/js/minipay.js') }}"></script>
@if(session()->get('error'))
    <script type="text/javascript" defer>
        Toastify({
            text: "{{ session()->get('error') }}",
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top",
            position: "center",
            stopOnFocus: true,
            style: {
                background: "#f06548",
            },
        }).showToast()
    </script>
@endif
@if(session()->get('success'))
    <script type="text/javascript" defer>
        Toastify({
            text: "{{ session()->get('success') }}",
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top",
            position: "center",
            stopOnFocus: true, 
            style: {
                background: "#0ab39c",
            },
        }).showToast()
    </script>
@endif