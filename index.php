<?php
  
// Memanggil function
require_once 'function.php';
  
$data = new rajaongkir(); // Inisiasi objek dari class rajaongkir. 
  
$kota = $data->get_city(); // Ambil data kota
  
$kota_array   = json_decode($kota, true);
  
// Cek api berdasarkan status jika akses api kita sudah dilimit perharinya
if ($kota_array['rajaongkir']['status']['code'] == 200) :
  $kota_result  = $kota_array['rajaongkir']['results'];
else :
  die('This key has reached the daily limit.');
endif;
?>
  
<!doctype html>
<html lang="en">
  
<head>
  
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  
  <title>Cek Ongkos Kirim</title>
  
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">
  
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</head>
  
<body>
  <div class="container">
    <div class="row mt-3">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            Cek Ongkos Kirim
          </div>
          <div class="card-body">
            <form id="form-cek-ongkir">
              <div class="form-group">
                <label for="kota_asal">Kota Asal</label>
                <select name="kota_asal" id="kota_asal" class="form-control">
                  <option value=""></option>
                  <?php foreach ($kota_array['rajaongkir']['results'] as $key => $value) : ?>
                    <option value="<?= $value['city_id']; ?>"><?= $value['city_name']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="kota_tujuan">Kota Tujuan</label>
                <select name="kota_tujuan" id="kota_tujuan" class="form-control">
                  <option value=""></option>
                  <?php foreach ($kota_array['rajaongkir']['results'] as $key => $value) : ?>
                    <option value="<?= $value['city_id']; ?>"><?= $value['city_name']; ?></option>
                  <?php endforeach; ?>
                  </select>
              </div>
              <div class="form-group">
                <label for="berat">Berat Kiriman (Gram)</label>
                <input type="number" id="berat" name="berat" class="form-control" min="1" max="30000" required="true">
              </div>
              <div class="form-group">
                <button type="submit" id="btn-periksa-ongkir" class="btn btn-primary">Periksa Ongkir</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header" id="hasil-pengecekan">
            Hasil Pengecekan Ongkir
          </div>
          <div class="card-body">
            <table id="tabel-hasil-pengecekan" class="display">
              <thead>
                <tr>
                  <th width="1%">No.</th>
                  <th>Kurir</th>
                  <th>Jenis Layanan</th>
                  <th>Tarif</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  
  
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
  
  <script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script>
    // Fungsi untuk mereset form dan select2
    function resetForm(form, select2 = []) {
      $('#' + form)[0].reset();
      if (select2.length > 0) {
        $.each(select2, function(key, value) {
          $('#' + value).val('').trigger('change');
        });
      }
    }
  
    // Saat halaman telah diload
    $(document).ready(function() {
      // Select2 untuk kota asal
      $('#kota_asal').select2({
        placeholder: "Pilih Kota Asal",
        theme: "bootstrap"
      });
      // Select2 untuk kota tujuan
      $('#kota_tujuan').select2({
        placeholder: "Pilih Kota Tujuan",
        theme: "bootstrap"
      });
    });
  
    // Event saat form cek ongkir di submit
    $('#form-cek-ongkir').on('submit', function(e) {
      e.preventDefault();
  
      $('#btn-periksa-ongkir').prop('disabled', true)
        .text('Loading...');
      let kota_asal = $('#kota_asal').select2('data')[0].text;
      let kota_tujuan = $('#kota_tujuan').select2('data')[0].text;
      let berat = $('#berat').val();
  
      $('#hasil-pengecekan').html(`Hasil Pengecekan Ongkir ${kota_asal} Ke ${kota_tujuan} Berat Kiriman @${berat} gram`);
  
      hasil_pengecekan(); // Panggil fungsi hasi pengecekan
    });
  
    // Fungsi hasil pengecekan untuk menampilkan data ke datatables
    function hasil_pengecekan() {
      $('#tabel-hasil-pengecekan').DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        responsive: true,
        ajax: {
          url: 'cost.php',
          type: "POST",
          data: {
            kota_asal: $('#kota_asal').val(),
            kota_tujuan: $('#kota_tujuan').val(),
            berat: $('#berat').val(),
          },
          complete: function(data) {
            resetForm('form-cek-ongkir', ['kota_asal', 'kota_tujuan']);
  
            $('#btn-periksa-ongkir').prop('disabled', false)
              .text('Periksa Ongkir');
  
          },
        },
        columnDefs: [{
            targets: [0],
            orderable: false,
          },
          {
            width: "1%",
            targets: [0],
          },
          {
            className: "dt-nowrap",
            targets: [1, 2],
          },
          {
            className: "dt-right",
            targets: [-1],
          },
        ],
  
      });
    }
  </script>
</body>
  
</html>