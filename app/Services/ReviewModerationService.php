<?php

namespace App\Services;

class ReviewModerationService
{
    /**
     * Pola kata-kata ujaran kebencian / pelecehan eksplisit yang dilarang keras.
     */
    protected array $severeSlurs = [
        '/\b(kafir|anjing|babi|bangsat|bajingan|kontol|memek|tai|asu|pantek|lonte)\b/i',
        '/\b(bunuh|bakar|serang|habisi|mati\s+lu|mampus)\b/i',
        '/\b(dasar\s+(cina|pribumi|jawa|sunda|batak|madura|papua|arab))\b/i',
        '/\b(anti\s+(islam|kristen|katolik|hindu|buddha|khonghucu))\b/i',
        '/\b(aliran\s+sesat|penistaan\s+agama)\b/i',
    ];

    /**
     * Pola spam promosi, slot judi online, dan tautan luar/phishing.
     */
    protected array $spamPatterns = [
        '/https?:\/\/[^\s]+/i',
        '/www\.[^\s]+/i',
        '/\b(bit\.ly|t\.co|tinyurl|wa\.me|t\.me)\b/i',
        '/\b(slot|gacor|maxwin|judi|casino|poker|togel|zeus|pragmatic)\b/i',
        '/\b(pinjol|dana\s+kaget|crypto|bitcoin)\b/i',
    ];

    /**
     * Pola kebocoran data pribadi sensitif (PII) seperti nomor kartu kredit atau NIK KTP.
     */
    protected array $sensitivePiiPatterns = [
        '/\b\d{4}[ -]?\d{4}[ -]?\d{4}[ -]?\d{4}\b/', // Format kartu kredit
        '/\b(nik|ktp)[:\s]*\d{16}\b/i',               // NIK KTP
        '/\b08[1-9][0-9]{7,10}\b/',                   // Nomor telepon luar dalam ulasan
    ];

    /**
     * Pola kata-kata yang memicu review manual (meragukan / potensi sengketa).
     */
    protected array $borderlineKeywords = [
        '/\b(penipu|scam|bohong|palsu|kw|abal[- ]abal|kecewa\s+berat)\b/i',
        '/\b(lapor\s+polisi|somasi|hukum|gugatan)\b/i',
    ];

    /**
     * Memoderasi konten ulasan pelanggan secara berlapis.
     *
     * @param string $reviewText
     * @param int $rating
     * @return array [ 'action' => 'publish'|'pending'|'reject', 'reason' => string|null, 'clean_text' => string ]
     */
    public function moderate(string $reviewText, int $rating): array
    {
        // 1. Sanitasi awal (Hilangkan tag HTML/JS untuk mencegah XSS)
        $cleanText = strip_tags(trim($reviewText));
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);

        // 2. Validasi panjang karakter bermakna
        $len = mb_strlen($cleanText);
        if ($len < 10) {
            return [
                'action'     => 'reject',
                'reason'     => 'Ulasan terlalu singkat (minimal 10 karakter bermakna).',
                'clean_text' => $cleanText,
            ];
        }

        if ($len > 1000) {
            return [
                'action'     => 'reject',
                'reason'     => 'Ulasan melebihi batas maksimum 1.000 karakter.',
                'clean_text' => $cleanText,
            ];
        }

        // 3. Deteksi Spam Pengulangan Karakter Berlebihan (misal: "baguuuusssssssssss" atau "??????????")
        if (preg_match('/(.)\1{9,}/u', $cleanText)) {
            return [
                'action'     => 'reject',
                'reason'     => 'Ulasan terdeteksi mengandung spam pengulangan karakter tidak wajar.',
                'clean_text' => $cleanText,
            ];
        }

        // 4. Deteksi Tautan Phishing dan Spam Promosi / Judi
        foreach ($this->spamPatterns as $pattern) {
            if (preg_match($pattern, $cleanText)) {
                return [
                    'action'     => 'reject',
                    'reason'     => 'Ulasan terdeteksi mengandung tautan URL, promosi eksternal, atau spam yang tidak diizinkan.',
                    'clean_text' => $cleanText,
                ];
            }
        }

        // 5. Deteksi Kebocoran PII (Data Pribadi Sensitif)
        foreach ($this->sensitivePiiPatterns as $pattern) {
            if (preg_match($pattern, $cleanText)) {
                return [
                    'action'     => 'reject',
                    'reason'     => 'Ulasan terdeteksi memuat informasi pribadi sensitif (seperti kontak pribadi atau nomor identitas/kartu).',
                    'clean_text' => $cleanText,
                ];
            }
        }

        // 6. Deteksi Ujaran Kebencian / SARA Kasar / Ancaman Eksplisit
        foreach ($this->severeSlurs as $pattern) {
            if (preg_match($pattern, $cleanText)) {
                return [
                    'action'     => 'reject',
                    'reason'     => 'Ulasan tidak memenuhi pedoman komunitas karena memuat ujaran kebencian, kata kasar, atau ancaman.',
                    'clean_text' => $cleanText,
                ];
            }
        }

        // 7. Deteksi Konten Meragukan (Borderline) -> Masuk Antrean Moderasi Admin
        foreach ($this->borderlineKeywords as $pattern) {
            if (preg_match($pattern, $cleanText)) {
                return [
                    'action'     => 'pending',
                    'reason'     => 'Memerlukan peninjauan manual admin terkait penggunaan kata kunci klaim sengketa/kualitas.',
                    'clean_text' => $cleanText,
                ];
            }
        }

        // 8. Deteksi Penggunaan Huruf Kapital Berlebihan (>75% huruf besar dan lebih dari 30 karakter)
        $lettersOnly = preg_replace('/[^a-zA-Z]/', '', $cleanText);
        if (strlen($lettersOnly) > 30) {
            $upperCount = strlen(preg_replace('/[^A-Z]/', '', $lettersOnly));
            if (($upperCount / strlen($lettersOnly)) > 0.75) {
                return [
                    'action'     => 'pending',
                    'reason'     => 'Penggunaan huruf kapital berlebihan (indikasi emosional atau spam). Menunggu tinjauan admin.',
                    'clean_text' => $cleanText,
                ];
            }
        }

        // 9. Ulasan Lolos Pemeriksaan -> Terbit Otomatis
        return [
            'action'     => 'publish',
            'reason'     => null,
            'clean_text' => $cleanText,
        ];
    }
}
