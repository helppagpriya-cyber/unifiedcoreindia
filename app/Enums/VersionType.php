<?php

namespace App\Enums;

enum VersionType: string
{
    case ORIGINAL_SCAN = 'original_scan';
    case PROCESSED_IMAGE = 'processed_image';
    case OCR_TEXT = 'ocr_text';
    case SEARCHABLE_PDF = 'searchable_pdf';
    case DELIVERY_PDF = 'delivery_pdf';
    case DELIVERY_TIFF = 'delivery_tiff';
    case DELIVERY_JPEG = 'delivery_jpeg';
    case OTHER = 'other';
}
