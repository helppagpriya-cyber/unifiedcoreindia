<?php

namespace App\Services;

use App\Models\ErpLicense;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class LegacyV1LicenseKeyGenerator
{
    private const EDITION_CODES = [
        1 => 'O',
        2 => 'P',
        3 => 'Q',
        4 => 'L',
        5 => 'M',
        6 => 'N',
        7 => 'J',
        8 => 'K',
        9 => 'I',
        10 => 'A',
        11 => 'B',
        12 => 'C',
        13 => 'D',
        14 => 'F',
        15 => 'G',
        16 => 'H',
        17 => 'E',
        18 => 'R',
        19 => 'S',
        20 => 'T',
        21 => 'U',
        22 => 'V',
        23 => 'W',
        24 => 'X',
        25 => 'Y',
        26 => '0',
        27 => '1',
        28 => '2',
        29 => '3',
        30 => '4',
    ];

    private const MODULE_GROUPS = [
        1 => ['GIRVI', 'KITTY', 'SMS', 'DFEED'],
        2 => ['BULL', 'EXPORT', 'SALARY', 'BRANCH'],
        3 => ['CURRENCY', 'RAPA', 'JOBWISE', 'DASSORT'],
    ];

    /**
     * Generate a Demo Key using the original VFP KTP=1 rules.
     */

    /**
     * Generate a Demo Key using the QST supplied by the user.
     */
    public function generateDemo(
        ErpLicense $license,
        string $qst,
        string|int $demoId,
    ): array {
        $license->loadMissing(['softwareVersion', 'modules']);

        $qst = trim($qst);
        $this->validateQst($qst);

        $demoId = (string) $demoId;

        if (! ctype_digit($demoId) || (int) $demoId < 1001 || (int) $demoId > 9999) {
            throw new InvalidArgumentException(
                'The Demo ID must be between 1001 and 9999.'
            );
        }

        $editionNumber = $this->getEditionNumber($license);

        $offset = $this->digitAt($qst, 3);

        // Demo uses the original KTP=1 ANS algorithm.
        $ans = $this->generateDemoAns($qst);

        $payload = $this->generatePayload(
            editionNumber: $editionNumber,
            modules: $license->modules,
            offset: $offset,
            maxUsers: (int) ($license->max_simultaneous_users ?? 0),
            maxCompanies: (int) ($license->max_companies ?? 0),
            salesPoints: (int) ($license->sales_points ?? 0),
            gstEnabled: (bool) $license->gst_enabled,
        );

        $key = $ans . $payload . '-' . str_pad($demoId, 4, '0', STR_PAD_LEFT);

        return [
            'key_type' => 'demo',
            'qst' => $qst,
            'ans' => $ans,
            'payload' => $payload,
            'license_key' => $key,
            'edition_number' => $editionNumber,
            'edition_code' => self::EDITION_CODES[$editionNumber],
            'lan_enabled' => (int) ($license->max_simultaneous_users ?? 0) > 1,
        ];
    }

    /**
     * Generate a Full Installation Key using the QST supplied by the user.
     */
    public function generateInstallation(
        ErpLicense $license,
        string $qst,
        string $softwareId,
    ): array {
        $license->loadMissing(['softwareVersion', 'modules']);

        $qst = trim($qst);
        $softwareId = trim($softwareId);

        if (strlen($softwareId) !== 6 || substr($softwareId, 0, 2) !== 'EG') {
            throw new InvalidArgumentException(
                'The Software ID must start with EG and contain exactly 6 characters.'
            );
        }

        // The VFP installation screen displays QST after normalization.
        // Applying the same transformations here is safe for an already
        // normalized QST and also protects against unnormalized input.
        $qst = $this->normalizeInstallationQst($qst);

        $this->validateQst($qst);

        $editionNumber = $this->getEditionNumber($license);

        // Offset must be read from the normalized QST.
        $offset = $this->digitAt($qst, 3);

        // Installation uses the original KTP=2 ANS algorithm.
        $ans = $this->generateInstallationAns($qst);

        $payload = $this->generatePayload(
            editionNumber: $editionNumber,
            modules: $license->modules,
            offset: $offset,
            maxUsers: (int) ($license->max_simultaneous_users ?? 0),
            maxCompanies: (int) ($license->max_companies ?? 0),
            salesPoints: (int) ($license->sales_points ?? 0),
            gstEnabled: (bool) $license->gst_enabled,
        );

        // KTP=2 initial installation key: ANS + eight-character payload.
        // No Demo ID suffix and no upgrade date encoding.
        $key = $ans . $payload;

        return [
            'key_type' => 'installation',
            'qst' => $qst,
            'ans' => $ans,
            'payload' => $payload,
            'license_key' => $key,
            'software_id' => $softwareId,
            'edition_number' => $editionNumber,
            'edition_code' => self::EDITION_CODES[$editionNumber],
            'lan_enabled' => (int) ($license->max_simultaneous_users ?? 0) > 1,
        ];
    }

    /**
     * Validate and return the supported legacy edition number.
     */
    private function getEditionNumber(ErpLicense $license): int
    {
        $editionNumber = (int) $license->softwareVersion?->legacy_edition_number;

        if (! isset(self::EDITION_CODES[$editionNumber])) {
            throw new InvalidArgumentException(
                'The selected software edition does not have a supported VFP edition number (1–30).'
            );
        }

        return $editionNumber;
    }

    /**
     * Build the eight-character configuration payload.
     */
    private function generatePayload(
        int $editionNumber,
        Collection $modules,
        int $offset,
        int $maxUsers,
        int $maxCompanies,
        int $salesPoints,
        bool $gstEnabled,
    ): string {
        $payload = self::EDITION_CODES[$editionNumber];

        foreach (self::MODULE_GROUPS as $moduleCodes) {
            $bits = '';

            foreach ($moduleCodes as $code) {
                $enabled = $modules->contains(
                    fn($module): bool =>
                    strtoupper((string) $module->code) === $code
                );

                $bits .= $enabled ? '1' : '0';
            }

            $arrayPosition = bindec($bits) + 1;

            $payload .= $this->asciiCharacter($arrayPosition + $offset + 64);
        }

        $payload .= $this->asciiCharacter($maxUsers + $offset + 65);
        $payload .= $this->asciiCharacter($maxCompanies + $offset + 65);
        $payload .= $this->asciiCharacter($salesPoints + $offset + 65);
        $payload .= $gstEnabled ? 'T' : 'F';

        return $payload;
    }

    /**
     * Original KTP=1 Demo ANS.
     *
     * The original VFP loop increments ATR by 2.
     * Therefore ATR=2 and ATR=4 cases are not reached.
     */
    private function generateDemoAns(string $qst): string
    {
        $length = strlen($qst);
        $ans = '';

        for ($position = 1; $position <= $length; $position += 2) {
            if ($position === 1) {
                $lastTwo = $this->vfpVal(substr($qst, -2));

                $value = str_starts_with($qst, 'P')
                    ? 11 + ($lastTwo / 13)
                    : 21 + ($lastTwo / 13);

                $ans .= $this->vfpRoundString($value);

                continue;
            }

            $currentDigit = $this->vfpVal(substr($qst, $position - 1, 1));
            $fourthDigit = $this->vfpVal(substr($qst, 3, 1));

            $value = ($currentDigit + ($fourthDigit / 2)) + 7.45;

            $ans .= $this->vfpRoundString($value);
        }

        return $ans;
    }

    /**
     * Original KTP=2 Installation ANS.
     *
     * Receives an already-normalized QST.
     */
    private function generateInstallationAns(string $qst): string
    {
        $length = strlen($qst);
        $ans = '';
        $position = 1;

        while ($position <= $length) {
            if ($position === 1) {
                $lastTwo = $this->vfpVal(substr($qst, -2));

                $value = str_starts_with($qst, 'P')
                    ? 11 + ($lastTwo / 13)
                    : 21 + ($lastTwo / 13);

                $ans .= $this->vfpRoundString($value);
                $position++;

                continue;
            }

            if ($position === 2) {
                $currentDigit = $this->vfpVal(substr($qst, 1, 1));
                $lastTwo = $this->vfpVal(substr($qst, -2));

                $value = (($currentDigit * $lastTwo) / 7) + 3.45;

                $ans .= $this->vfpRoundString($value);
                $position += 2;

                continue;
            }

            if ($position === 4) {
                $digit = $this->vfpVal(substr($qst, 3, 1));

                $ans .= $this->asciiCharacter($digit + 65);
                $position += 2;

                continue;
            }

            $currentDigit = $this->vfpVal(substr($qst, $position - 1, 1));
            $fourthDigit = $this->vfpVal(substr($qst, 3, 1));

            $value = ($currentDigit + ($fourthDigit / 2)) + 7.45;

            $ans .= $this->vfpRoundString($value);
            $position += 2;
        }

        return $ans;
    }

    /**
     * Apply the original VFP installation QST transformations.
     */
    private function normalizeInstallationQst(string $qst): string
    {
        $lastDigit = $this->vfpVal(substr($qst, -1));

        if ($lastDigit > 4) {
            $qst = substr($qst, 0, -1) . ($lastDigit - 5);
        }

        $tensDigit = $this->vfpVal(substr($qst, -2, 1));

        if ($tensDigit > 2) {
            $qst = substr($qst, 0, -2) . '0' . substr($qst, -1);
        }

        $hundredsDigit = $this->vfpVal(substr($qst, -3, 1));

        if ($hundredsDigit > 2) {
            $qst = substr($qst, 0, -3) . '1' . substr($qst, -2);
        }

        return $qst;
    }

    private function validateQst(string $qst): void
    {
        if (
            strlen($qst) < 6 ||
            ! str_starts_with($qst, 'P') ||
            ! ctype_digit(substr($qst, 1))
        ) {
            throw new InvalidArgumentException(
                'Invalid QST. Enter the complete QST displayed by EGJEWEL.'
            );
        }

        if (! ctype_digit(substr($qst, 2, 1))) {
            throw new InvalidArgumentException(
                'The third character of generated QST must be numeric.'
            );
        }
    }

    /**
     * VFP VAL(): read the leading numeric portion of a string.
     */
    private function vfpVal(string $value): int
    {
        if (preg_match('/^\s*([+-]?(?:\d+(?:\.\d*)?|\.\d+))/', $value, $matches)) {
            return (int) ((float) $matches[1]);
        }

        return 0;
    }

    /**
     * VFP ROUND(value, 0), followed by STR() and ALLTRIM().
     */
    private function vfpRoundString(float $value): string
    {
        return (string) (int) round($value, 0, PHP_ROUND_HALF_UP);
    }

    private function asciiCharacter(int $value): string
    {
        if ($value < 1 || $value > 255) {
            throw new InvalidArgumentException(
                "The legacy key produced an invalid character code: {$value}."
            );
        }

        return chr($value);
    }

    private function digitAt(string $value, int $position): int
    {
        return $this->vfpVal(substr($value, $position - 1, 1));
    }
}
