<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ReviewFixtures extends Fixture
{
    private const REVIEWS = [
        ['Aurora Consulting Kft.', 5, 'nagy.eszter@example.com', 2, 'Pontosak, felkészültek, és végig érthetően kommunikáltak. Bátran ajánlom.'],

        ['Zephyr Solutions Kft.', 5, 'kovacs.daniel@example.com', 1, 'A határidőt tartották, a végeredmény jobb lett, mint amit kértünk.'],
        ['Zephyr Solutions Kft.', 5, 'toth.marton@example.com', 6, 'Rugalmasak voltak, amikor menet közben változtattunk a specifikáción.'],
        ['Zephyr Solutions Kft.', 4, 'szabo.anna@example.com', 12, 'Jó munka, csak az induláskor volt egy kis egyeztetési csúszás.'],
        ['Zephyr Solutions Kft.', 5, 'varga.peter@example.com', 20, 'Több céggel dolgoztunk már, ők voltak a legprofibbak.'],
        ['Zephyr Solutions Kft.', 4, 'feher.judit@example.com', 33, 'Korrekt ár-érték arány, elégedettek vagyunk.'],

        ['Nimbus Média Kft.', 5, 'balogh.gabor@example.com', 3, 'Kreatív ötletek, gyors reagálás. Jövőre is velük dolgozunk.'],
        ['Nimbus Média Kft.', 4, 'horvath.kata@example.com', 9, 'A kampány hozta a számokat, a riportolás lehetne részletesebb.'],
        ['Nimbus Média Kft.', 4, 'lukacs.bence@example.com', 17, 'Jó csapat, néha kicsit lassú a visszajelzés.'],
        ['Nimbus Média Kft.', 4, 'racz.dora@example.com', 28, 'Szolid, megbízható kiszolgálás.'],

        ['Kék Delfin Utazási Iroda', 4, 'meszaros.tamas@example.com', 5, 'Zökkenőmentes ügyintézés, a szállás pontosan olyan volt, mint a leírásban.'],
        ['Kék Delfin Utazási Iroda', 4, 'simon.orsolya@example.com', 14, 'Kedves ügyintéző, jó ajánlatok. Az online felület fejleszthető.'],
        ['Kék Delfin Utazási Iroda', 3, 'nemeth.laszlo@example.com', 25, 'Az utazás rendben volt, de a repülőtéri transzfer késett.'],

        ['Pannon Digital Zrt.', 5, 'kiss.viktoria@example.com', 4, 'Kiváló technikai tudás, minden kérdésünkre volt válaszuk.'],
        ['Pannon Digital Zrt.', 5, 'olah.zsolt@example.com', 11, 'A projekt időben és kereten belül zárult.'],
        ['Pannon Digital Zrt.', 1, 'papp.ildiko@example.com', 19, 'Hetekig nem kaptunk választ, végül máshol oldottuk meg.'],
        ['Pannon Digital Zrt.', 1, 'juhasz.andras@example.com', 30, 'A számla nem egyezett az árajánlattal, az egyeztetés nehézkes volt.'],

        ['Bástya Építő Kft.', 3, 'boros.krisztina@example.com', 7, 'A munka minősége rendben, a takarítás után viszont maradt utánuk törmelék.'],
        ['Bástya Építő Kft.', 2, 'fodor.gergely@example.com', 16, 'Kétszer csúszott a határidő, a kommunikáció akadozott.'],
        ['Bástya Építő Kft.', 1, 'somogyi.reka@example.com', 27, 'A vállalt munkából több elem nem készült el, reklamálni kellett.'],
    ];

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        foreach (self::REVIEWS as [$companyName, $rating, $authorEmail, $daysAgo, $reviewText]) {
            $review = (new Review())
                ->setCompanyName($companyName)
                ->setRating($rating)
                ->setAuthorEmail($authorEmail)
                ->setReviewText($reviewText)
                ->setCreatedAt($now->modify(sprintf('-%d days', $daysAgo)));

            $manager->persist($review);
        }

        $manager->flush();
    }
}
