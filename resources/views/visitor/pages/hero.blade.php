<section id="home-section" class="hero">
    <h3 class="vr">Koperasi Simpan Pinjam</h3>
    <div class="home-slider js-fullheight owl-carousel">

        <!-- SLIDE 1 -->
        <div class="slider-item js-fullheight">
            <div class="overlay"></div>
            <div class="container-fluid p-0">
                <div class="row d-md-flex no-gutters slider-text js-fullheight align-items-center justify-content-end"
                    data-scrollax-parent="true">

                    <div class="one-third order-md-last img js-fullheight"
                        style="background-image:url('{{ asset('visitor/images/bg_1.jpg') }}');">
                        <div class="overlay"></div>
                    </div>

                    <div class="one-forth d-flex js-fullheight align-items-center ftco-animate"
                        data-scrollax=" properties: { translateY: '70%' }">

                        <div class="text">
                            <span class="subheading">Selamat Datang di</span>

                            <h1 class="mb-4 mt-3">
                                Koperasi <span>Simpan Pinjam</span>
                            </h1>

                            <p>
                                Solusi keuangan terpercaya untuk anggota koperasi.
                                Nikmati layanan simpanan, pinjaman, dan transaksi
                                yang mudah, cepat, dan aman secara digital.
                            </p>

                            <p>
                                <a href="{{ route('login') }}"
                                   class="btn btn-primary px-5 py-3 mt-3">
                                    Login Anggota
                                </a>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDE 2 -->
        <div class="slider-item js-fullheight">
            <div class="overlay"></div>
            <div class="container-fluid p-0">

                <div class="row d-flex no-gutters slider-text js-fullheight align-items-center justify-content-end"
                    data-scrollax-parent="true">

                    <div class="one-third order-md-last img js-fullheight"
                        style="background-image:url('{{ asset('visitor/images/bg_2.jpg') }}');">
                        <div class="overlay"></div>
                    </div>

                    <div class="one-forth d-flex js-fullheight align-items-center ftco-animate"
                        data-scrollax=" properties: { translateY: '70%' }">

                        <div class="text">

                            <span class="subheading">
                                Layanan Keuangan Digital
                            </span>

                            <h1 class="mb-4 mt-3">
                                Simpanan <span>Aman</span> & Pinjaman
                                <span>Mudah</span> untuk Anggota
                            </h1>

                            <p>
                                Kelola simpanan, ajukan pinjaman, pantau cicilan,
                                dan lihat riwayat transaksi langsung melalui
                                sistem koperasi berbasis web yang modern.
                            </p>

                            <p>
                                <a href="{{ route('register') }}"
                                   class="btn btn-primary px-5 py-3 mt-3">
                                    Daftar Menjadi Anggota
                                </a>
                            </p>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>
