<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260616103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Backfill Arabic catalogue, slider and promotion fields for existing demo data.';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $this->fill('category', 1, ['name_ar' => 'فساتين', 'description_ar' => 'تشكيلة فساتين أنيقة لكل المناسبات']);
        $this->fill('category', 2, ['name_ar' => 'قمصان وتيشيرتات', 'description_ar' => 'توبات وتيشيرتات وقمصان عصرية']);
        $this->fill('category', 3, ['name_ar' => 'قطع سفلية', 'description_ar' => 'تنانير وشورتات وسراويل نسائية']);
        $this->fill('category', 4, ['name_ar' => 'أطقم', 'description_ar' => 'إطلالات كاملة ومنسقة']);
        $this->fill('category', 5, ['name_ar' => 'لانجري', 'description_ar' => 'ملابس داخلية ولانجري راق']);
        $this->fill('category', 6, ['name_ar' => 'إكسسوارات', 'description_ar' => 'حقائب ومجوهرات وإكسسوارات موضة']);
        $this->fill('category', 7, ['name_ar' => 'أحذية', 'description_ar' => 'أحذية نسائية لإكمال إطلالتك']);

        $products = [
            1 => ['فستان ميدي أنيق', 'فستان ميدي من الكريب بياقة V. قصة ناعمة ومنسدلة، مثالي للمناسبات الخاصة.', 'أسود، بوردو، أخضر زمردي'],
            2 => ['فستان صيفي مزهر', 'فستان خفيف بطبعة زهور، أكمام منفوخة وحزام معقود. مثالي للأيام المشمسة.', 'متعدد الألوان، وردي، أزرق سماوي'],
            3 => ['فستان كوكتيل ساتان', 'فستان قصير من الساتان بظهر مفتوح. أنيق وراقي لسهراتك.', 'بوردو، أسود، شامبانيا'],
            4 => ['قميص حرير', 'قميص فاخر من الحرير الطبيعي بياقة قميص وأكمام طويلة. أنيق وخالد.', 'أبيض، عاجي، وردي باهت'],
            5 => ['تيشيرت أساسي بياقة V', 'تيشيرت من القطن العضوي بياقة V وقصة مضبوطة. قطعة أساسية في كل خزانة.', 'رمادي، أبيض، أسود'],
            6 => ['توب بأكمام منفوخة', 'توب عصري بأكمام منفوخة وياقة مربعة. مثالي لإطلالة حديثة.', 'وردي باهت، ليلكي، بيج'],
            7 => ['جينز سليم بخصر عال', 'جينز سليم مطاطي بخصر عال، قصة مريحة وتبرز القوام. لون أزرق متوسط.', 'أزرق، أسود، بيج'],
            8 => ['تنورة ميدي بكسرات', 'تنورة ميدي بكسرات من قماش ناعم. أنيقة وسهلة الارتداء يوميا.', 'بيج، جملي، أسود'],
            9 => ['شورت كتان', 'شورت قصير من الكتان الطبيعي، قصة مستقيمة وجيب أنيق. مثالي للصيف.', 'عاجي'],
            10 => ['حمالة صدر دانتيل', 'حمالة صدر بوش أب من دانتيل ناعم مع حشوة قابلة للإزالة. راحة وجاذبية.', 'أسود'],
            11 => ['سروال داخلي بوكسر دانتيل', 'سروال داخلي بوكسر من دانتيل مطاطي، مريح وأنيق.', 'أبيض'],
            12 => ['حذاء كعب 8 سم', 'حذاء كعب من جلد لامع بكعب رفيع 8 سم. أناقة ورقي.', 'أسود'],
            13 => ['حذاء رياضي جلد أبيض', 'حذاء رياضي من جلد حقيقي بنعل مريح وتصميم بسيط. عملي وأنيق.', 'أبيض'],
            14 => ['حقيبة يد بسلسلة ذهبية', 'حقيبة يد بسلسلة ذهبية، قسم رئيسي وجيب داخلي.', 'جملي'],
            15 => ['عقد لؤلؤ طبيعي', 'عقد أنيق بلؤلؤ طبيعي وقفل ذهبي. قطعة خالدة وراقية.', 'أبيض'],
        ];

        foreach ($products as $id => [$name, $description, $colors]) {
            $this->fill('product', $id, [
                'name_ar' => $name,
                'description_ar' => $description,
                'color_ar' => $colors,
            ]);
        }

        $sizeNames = [
            1 => '32', 2 => '34', 3 => '36', 4 => '38', 5 => '40', 6 => '42', 7 => '44', 8 => '46', 9 => '48',
            10 => 'صغير جدا', 11 => 'صغير', 12 => 'متوسط', 13 => 'كبير', 14 => 'كبير جدا',
            15 => '35', 16 => '36', 17 => '37', 18 => '38', 19 => '39', 20 => '40', 21 => '41', 22 => '42',
        ];

        foreach ($sizeNames as $id => $name) {
            $this->fill('size', $id, ['name_ar' => $name]);
        }

        $this->fill('slider_image', 1, [
            'title_ar' => 'تشكيلة الربيع الجديدة',
            'description_ar' => 'اكتشفي أحدث صيحات الموضة النسائية لهذا الموسم',
            'button_text_ar' => 'اكتشفي',
        ]);
        $this->fill('slider_image', 2, [
            'title_ar' => 'فساتين الصيف',
            'description_ar' => 'فساتين خفيفة وأنيقة للتألق هذا الصيف',
            'button_text_ar' => 'عرض الفساتين',
        ]);
        $this->fill('slider_image', 3, [
            'title_ar' => 'تخفيضات حصرية',
            'description_ar' => 'حتى 50% على مجموعة مختارة من المنتجات',
            'button_text_ar' => 'استفيدي من التخفيضات',
        ]);
        $this->fill('slider_image', 4, [
            'title_ar' => 'توصيل مجاني',
            'description_ar' => 'توصيل مجاني بداية من 60 د.ت شراء',
            'button_text_ar' => 'معرفة المزيد',
        ]);

        $this->fill('promotion', 1, ['title_ar' => 'عرض تجريبي', 'description_ar' => 'وصف العرض التجريبي']);
        $this->fill('promotion', 2, ['title_ar' => 'عرض خاص', 'description_ar' => 'تفاصيل العرض الخاص']);
    }

    public function down(Schema $schema): void
    {
        $this->clear('category', ['name_ar', 'description_ar']);
        $this->clear('product', ['name_ar', 'description_ar', 'color_ar']);
        $this->clear('size', ['name_ar']);
        $this->clear('slider_image', ['title_ar', 'description_ar', 'button_text_ar']);
        $this->clear('promotion', ['title_ar', 'description_ar']);
    }

    /**
     * @param array<string, string> $values
     */
    private function fill(string $table, int $id, array $values): void
    {
        $sets = [];
        foreach ($values as $column => $value) {
            $quoted = $this->connection->quote($value);
            $sets[] = sprintf('%1$s = IF(%1$s IS NULL OR %1$s = \'\', %2$s, %1$s)', $column, $quoted);
        }

        $this->addSql(sprintf('UPDATE %s SET %s WHERE id = %d', $table, implode(', ', $sets), $id));
    }

    /**
     * @param list<string> $columns
     */
    private function clear(string $table, array $columns): void
    {
        $sets = array_map(static fn (string $column): string => $column . ' = NULL', $columns);
        $this->addSql(sprintf('UPDATE %s SET %s', $table, implode(', ', $sets)));
    }
}
