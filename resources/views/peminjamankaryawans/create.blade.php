@extends('patrial.template')
@section('content')
<style>
    form {
        margin-top: 3rem;
        /* Sesuaikan nilai sesuai kebutuhan desain Anda */
    }

    #cart {
        margin-top: 1rem;
    }

    #cart table {
        width: 100%;
        border-collapse: collapse;
    }

    #cart th,
    #cart td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
</style>

<body>
    <div class="table-responsive">
        <h3 class="text-center"> <b> Create Transaksi </b></h3>
        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" id="form-createe" action="{{ route('peminjamankaryawans.store') }}">
            {{ csrf_field() }}
            <div class="container mt-3">
                <div class="row">
                    <div class="demo-vertical-spacing demo-only-element col-md-19 my-3">

                        <div class="form-group mb-3">
                            <select name="Nik" class="form-control" id="NikSelect">
                                <option value="">-- Select Nik --</option>
                                @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->Nik }}" data-nama="{{ $karyawan->NamaKaryawan }}" data-jabatan="{{ $karyawan->jabatan }}">{{ $karyawan->Nik }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon11">Karyawan</span>
                            <input type="text" id="Karyawan" class="form-control" name="NamaKaryawan" placeholder="NamaKaryawan" aria-label="NamaKaryawan">
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon11">Jabatan</span>
                            <input type="text" id="Jabatan" class="form-control" name="Jabatan" placeholder="Jabatan" aria-label="Jabatan">
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <h4 class="me-4">Daftar Peminjaman</h4>
                            <button type="button" id="addToCartBtn" class="btn btn-success">Tambah</button>
                        </div>

                        <!-- Bagian Keranjang -->
                        <div id="cart" class="mb-3">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="text-center">Barang</th>
                                        <th class="text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <!-- Keranjang akan ditampilkan di sini -->
                                </tbody>
                            </table>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon11">Petugas</span>
                            <input type="text" class="form-control" name="Petugas" id="Petugas" placeholder="Petugas" aria-label="Petugas" value="{{ ucwords(Auth::user()->name) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="menetap" checked>
                                <label class="form-check-label">Menetap</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="kembali">
                                <label class="form-check-label">Kembali</label>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon11">Batas Pengembalian</span>
                            <input type="date" class="form-control" name="BatasPengembalian" id="BatasPengembalian" placeholder="Batas Pengembalian" aria-label="Batas Pengembalian" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon11">Tanggal Pengembalian</span>
                            <input type="date" class="form-control" name="TanggalPengembalian" placeholder="Tanggal Pengembalian" aria-label="Tanggal Pengembalian" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon11">Jumlah Kembali</span>
                            <input type="number" class="form-control" name="JumlahKembali" placeholder="Jumlah Kembali" aria-label="Jumlah Kembali" disabled>
                        </div>

                        <div class="row justify-content-end mt-2">
                            <div class="col-auto">
                                <input type="hidden" id="cetak" name="cetak">
                                <button type="button" id="create" value="submit" class="btn btn-primary mr-2">Create Transaksi</button>
                            </div>
                        </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#NikSelect').select2({
                placeholder: 'Select Nik'
            });
            $('#NikSelect').change(function() {
                var nik = $(this).val();

                if (nik) {
                    $.ajax({
                        url: '/getKaryawanData/' + nik, // Ganti dengan URL endpoint Anda
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#Karyawan').val(data.NamaKaryawan || '').prop('readonly', true);
                            $('#Jabatan').val(data.Jabatan || '').prop('readonly', true);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    // Reset nilai jika NIK tidak dipilih
                    $('#Karyawan, #Jabatan').val('').prop('readonly', false);
                }
            });

            const addToCartBtn = document.getElementById('addToCartBtn');
            const cartBody = document.getElementById('cartBody');

            addToCartBtn.addEventListener('click', function() {
                const length = cartBody.children.length;
                // Tambahkan data ke keranjang
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>
                    <select name="Order[${length}][BarangID]" class="form-control" id="BarangID" placeholder="Barang">
                        <option value="">--Select Barang--</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->BarangID }}">{{ $barang->NamaBarang }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" name="Order[${length}][Jumlah]" placeholder="-- Jumlah --" aria-label="-- Jumlah " />
                </td>
            `;
                cartBody.appendChild(row);
            });

            const statusRadio = document.querySelectorAll('input[name="status"]');
            const batasPengembalianField = document.querySelector('input[name="BatasPengembalian"]');
            const tanggalPengembalianField = document.querySelector('input[name="TanggalPengembalian"]');
            const jumlahKembaliField = document.querySelector('input[name="JumlahKembali"]');

            statusRadio.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (this.value === 'menetap') {
                        batasPengembalianField.value = ''; // Clear the value
                        batasPengembalianField.setAttribute('disabled', true);
                        tanggalPengembalianField.value = '';
                        tanggalPengembalianField.setAttribute('disabled', true);
                        jumlahKembaliField.value = '';
                        jumlahKembaliField.setAttribute('disabled', true);
                    } else if (this.value === 'kembali') {
                        batasPengembalianField.removeAttribute('disabled');
                        tanggalPengembalianField.removeAttribute('disabled');
                        jumlahKembaliField.removeAttribute('disabled');
                    }
                });
            });
        });


        $(document).on("click", "#create", function() {
            Swal.fire({
                title: "Hallo!",
                text: "Are you sure you want to make a transaction?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to print a transaction receipt?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#cetak').val('Cetak');
                        } else {
                            $('#cetak').val('Tidak');
                        }
                        $('#form-createe').submit();
                    });
                }
            });
        })
    </script>
    @endsection