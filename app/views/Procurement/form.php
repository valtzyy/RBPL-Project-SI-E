<h2>Form Permintaan Pengadaan Kendaraan</h2>

<form method="POST" action="/procurement/store">

    <div>
        <label>Kode Permintaan</label>
        <input
            type="text"
            name="request_code"
            required
        >
    </div>

    <br>

    <button type="submit">
        Kirim Permintaan
    </button>

</form>