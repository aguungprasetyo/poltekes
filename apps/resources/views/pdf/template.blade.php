<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=sty, initial-scale=1.0">
    <title>eLogbook Poltekes Yogyakarta</title>
    <style>
        :root {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-items: center;
            max-width: 655px;
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 0.9rem;
            color: #212121;
            padding: 2rem 4rem 4rem 4rem;
        }

        header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 4rem 5rem 2rem 0rem;
            /* margin-bottom: 4rem; */
            font-size: 0.8rem;
            /* border-bottom: 1px solid #212121; */
        }

        header img {
            max-width: 20%;
            height: auto;
            display: block;
            margin: 0 auto;

        }

        section {
            margin-bottom: 1rem;
        }

        h1,
        h3 {
            margin: 0;
        }

        h1 {
            font-size: 1.6rem;
        }

        p {
            margin-top: 0.5rem;
            line-height: 1.2rem;
        }

        ul {
            margin: 0;
            padding-left: 0;
        }

        li {
            list-style-type: none;
        }

        .box h3 {
            margin-bottom: 0.5rem;
        }

        ul li p {
            line-height: 0.4rem;
        }

        .box {

            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 1.2rem 1rem;
            border: 1px solid #f5f5f5;
            border-radius: 0.5rem;
        }

        .box-2 {

            display: flex;
            justify-content: space-between;
            align-content: flex-start;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            gap: 1rem;
            background-color: #2ABAAD;
            border: 1px solid #f5f5f5;
            color: #fefefe;

        }

        .box-2 p {
            margin: 0;
        }

        .img-dokumentasi {
            padding-top: 0.4rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        footer {
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        /* Media query for print */
        @media print {
            @page {
                size: A4;
                margin: 12mm;
                /* Margin for A4 */
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
                font-size: 0.8rem;
                color: #212121;
            }

            header {
                padding: 0 5rem 1rem 0rem;
                font-size: 0.8rem;
            }

            h1 {
                font-size: 1.2rem;
            }

            .container {
                width: 100%;
                box-sizing: border-box;
            }

            header {
                margin-bottom: 1rem;
            }

            section {
                margin-bottom: 1rem;
            }

            img {
                max-width: 100%;
                height: auto;
            }

            .box-2 {

                color: #212121;
                border: 1px solid #f5f5f5;
            }

            img {
                max-width: 48%;
                height: auto;
                object-fit: cover;
            }

        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Document -->
        <table style="margin-bottom: 2rem">
            <tr>
                <td style="width: 20%">
                    <img src="{{ $data['icon'] }}" style="width: 100%" alt="">
                </td>
                <td>
                    <h1 style="margin-left: 20px">LAPORAN KEGIATAN MAGANG POLITEKNIK KESEHATAN YOGYAKARTA</h1>
                </td>
            </tr>
        </table>

        <!-- Kegiatan / Hari / Tanggal -->
        <section class="box-2">
            <h3>Kegiatan Ke-1</h3>
            <p>{{ date('l, d F Y H:i', strtotime($data['record']->created_at)) }}</p>
        </section>

        <!-- Daftar Kelompok -->
        <section class="box">
            <h3>Daftar Kelompok</h3>
            <ul>
                @foreach ($data['student'] as $index => $value)
                    <li>
                        <p>{{ $index }} - {{ $value }}</p>
                    </li>
                @endforeach
            </ul>
        </section>


        <!-- box -->
        <div class="box">
            <!-- Judul Kegiatan -->
            <section>
                <h3>Judul Kegiatan</h3>
                <p>
                    {{ $data['record']->title }}
                </p>
            </section>

            <!-- Deskripsi Kegiatan -->
            <section>
                <h3>Deskripsi Kegiatan</h3>
                <p>
                    {!! $data['record']->log !!}
                </p>
            </section>

            <!-- Foto Dokumentasi -->
            <section>
                <h3>Dokumentasi</h3>
                <section class="img-dokumentasi">
                    @foreach ($data['attachment'] as $image)
                        <img src="{{ $image }}" alt="">
                    @endforeach
                </section>
            </section>

            <!-- Tanggapan Dosen -->
            <section>
                <h3>Tanggapan</h3>
                <p>
                    {!! $data['record']->comment !!}
                </p>
            </section>
        </div>
    </div>
    <footer>
        <p>&copy; 2023 Politeknik Kesehatan Yogyakarta</p>
    </footer>
</body>

</html>
