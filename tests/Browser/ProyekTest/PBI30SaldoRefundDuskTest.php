<?php

namespace Tests\Browser\ProyekTest;

use App\Events\ProyekDibatalkan;
use App\Models\MutasiSaldo;
use App\Models\SaldoDonatur;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use Tests\DuskTestCase;

class PBI30SaldoRefundDuskTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    use DatabaseTruncation;

    public function test_refund_otomatis_massal_sukses(): void
    {
        [$proyek, $donors] = $this->createCancelledProject(3);
        [$donorA, $donorB, $donorC] = $donors;
        $this->addSuccessfulDonation($proyek, $donorA, 100000);
        $this->addSuccessfulDonation($proyek, $donorB, 75000);
        $this->addSuccessfulDonation($proyek, $donorC, 125000);

        event(new ProyekDibatalkan($proyek));

        $this->assertSame(100000.0, (float) SaldoDonatur::where('id_donatur', $donorA->getKey())->value('saldo'));
        $this->assertSame(75000.0, (float) SaldoDonatur::where('id_donatur', $donorB->getKey())->value('saldo'));
        $this->assertSame(125000.0, (float) SaldoDonatur::where('id_donatur', $donorC->getKey())->value('saldo'));
    }

    public function test_refund_multi_donation_satu_donatur(): void
    {
        [$proyek, [$donor]] = $this->createCancelledProject(1);
        $this->addSuccessfulDonation($proyek, $donor, 50000);
        $this->addSuccessfulDonation($proyek, $donor, 75000);

        event(new ProyekDibatalkan($proyek));

        $this->assertSame(125000.0, (float) SaldoDonatur::where('id_donatur', $donor->getKey())->value('saldo'));
        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $donor->getKey(),
            'tipe' => 'refund',
            'nominal' => 125000,
            'referensi_proyek_id' => $proyek->id,
        ]);
    }

    public function test_penanganan_kegagalan_refund_salah_satu_donatur(): void
    {
        [$proyek, $donors] = $this->createCancelledProject(2);
        [$donorA, $donorB] = $donors;
        $this->addSuccessfulDonation($proyek, $donorA, 50000);
        $this->addSuccessfulDonation($proyek, $donorB, 60000);
        $donorB->delete();

        event(new ProyekDibatalkan($proyek));

        $this->assertSame(50000.0, (float) SaldoDonatur::where('id_donatur', $donorA->getKey())->value('saldo'));
    }

    public function test_keamanan_proyek_aktif_tidak_di_refund(): void
    {
        [$proyek, [$donor]] = $this->createCancelledProject(1);
        $this->addSuccessfulDonation($proyek, $donor, 100000);

        $proyek->update(['status' => 'eksekusi']);

        $this->assertSame(0.0, (float) SaldoDonatur::where('id_donatur', $donor->getKey())->value('saldo'));
        $this->assertFalse(MutasiSaldo::where('id_donatur', $donor->getKey())->where('tipe', 'refund')->exists());
    }

}
