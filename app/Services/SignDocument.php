<?php

namespace App\Services;
use Illuminate\Support\Facades\Hash;


class SignDocument {
    
    public function sign($decryptor, $user, $f) {

        try {
            $dec_priv = $user->getAsymmetricKey($decryptor, 'priv');
        } catch (\Throwable $th) {
            return null;
        }
        $dokumen_hash = Hash::make($f);
        $dokumen_hash = $dec_priv->sign($dokumen_hash);
        //then encrypt the hash

        //regex pattern to get pdf object -> 2 0 obj, for example
        $pattern = '/(\d+) (\d+) obj/';
        
        $maxObj = -1;
        $offsetEOF = -1;

        // will get the maximum object number
        // making sure that the pdf file doesn't use the object number, iterating by 1
        if (preg_match_all($pattern, $f, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if($match[1] > $maxObj){
                    $maxObj = $match[1];
                }
            }
            //find offset of last %%EOF
            if(preg_match("/%%EOF$/", $f, $matches, PREG_OFFSET_CAPTURE)) {
                $offsetEOF = $matches[0];
            }
            else return null;

        }
        else return null;
        //iterate +1 to use as a new object later
        $maxObj += 1;
        $offsetEOF = $offsetEOF[1];
        
        //hopefully stitching the string containing the dig sig
        $before = substr($f, 0, $offsetEOF);
        $after = substr($f, $offsetEOF);


        $time = now()->format('D:YmdHisO');
        //continue trying to understand how to embed dig sig to pdf
        $digSigString = $maxObj . " 0 obj
        <</F 132/Type/Annot/Subtype/Widget/Rect[0 0 0 0]/FT/Sig
        /DR<<>>/T(signature)/V ". $maxObj+1 . " 0 R/P 4 0 R/AP<</N 2 0 R>>>>
        endobj\n" . $maxObj + 1 . " 0 obj
        <</Contents <" . $dokumen_hash;

        $offsetpt1 = strpos($digSigString, $dokumen_hash);
        
        $digSigStringpt2 = ">/Type/Sig/SubFilter/adbe.pkcs8.detached/Location(Indonesia)/M(" . $time . ")/ByteRange [0 ".$offsetEOF + $offsetpt1 . " " . $offsetEOF + $offsetpt1 + 32 . " 50 ]/Filter/Adobe.PPKLite/Reason(Test)/ContactInfo()>>
        endobj";
        $f = $before . $digSigString . $digSigStringpt2 . $after;

        return $f;
    }
}