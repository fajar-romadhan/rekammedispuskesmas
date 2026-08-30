    </div>
    <!-- #/ content-body container-fluid -->

</div>
<!--**********************************
    Content body end
***********************************-->

<!--**********************************
    Footer start
***********************************-->
<div class="footer">
    <div class="copyright">
        <p>&copy; <?= date('Y'); ?> Puskesmas Mendis &mdash; Sistem Informasi Rekam Medis</p>
    </div>
</div>
<!--**********************************
    Footer end
***********************************-->

</div>
<!--**********************************
    Main wrapper end
***********************************-->

<!--**********************************
    Scripts Quixlab (jQuery + Bootstrap4
    sudah dimuat di header.php)
***********************************-->
<script src="<?= $main_url ?>asset/quixlab/js/custom.min.js"></script>
<script src="<?= $main_url ?>asset/quixlab/js/settings.js"></script>
<script src="<?= $main_url ?>asset/quixlab/js/gleek.js"></script>
<script src="<?= $main_url ?>asset/quixlab/js/styleSwitcher.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

<!-- Chart.js (dipakai grafik dashboard Administrator) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js"
    integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp"
    crossorigin="anonymous"></script>

<?php if (userHasRole(ROLE_ADMIN) && isset($bln_ini) && isset($list_data)) { ?>

<script>
let blnSkrg = <?= $bln_ini ?>;

let nmBln = [
    'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

let listBln = [];

for (let i = 0; i < blnSkrg; i++) {
    listBln.push(nmBln[i]);
}

(() => {
    'use strict'

    const ctx = document.getElementById('myChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: listBln,
                datasets: [{
                    label: 'Pendaftaran Pasien',
                    data: [<?= implode(',', $list_data); ?>],
                    lineTension: 0,
                    backgroundColor: 'transparent',
                    borderColor: '#7460ee',
                    borderWidth: 3,
                    pointBackgroundColor: '#7460ee'
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
})();
</script>

<?php } ?>

</body>
</html>
