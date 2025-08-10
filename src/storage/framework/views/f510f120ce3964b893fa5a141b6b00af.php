<?php $__env->startComponent('mail::message'); ?>
# Invoice Pesanan #<?php echo new \Illuminate\Support\EncodedHtmlString($pesanan->nomor_pesanan); ?>


Halo **<?php echo new \Illuminate\Support\EncodedHtmlString($pesanan->profile->nama_lengkap); ?>**,  
Terima kasih telah melakukan pembayaran di Apotiku. Berikut detail pesanan Anda:

<?php $__env->startComponent('mail::table'); ?>
| Produk | Qty | Harga Satuan | Subtotal |
|:-------|:--:|------------:|---------:|
<?php $__currentLoopData = $pesanan->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
| <?php echo new \Illuminate\Support\EncodedHtmlString($item->obat->nama_obat); ?> | <?php echo new \Illuminate\Support\EncodedHtmlString($item->qty); ?> | Rp<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($item->obat->harga,0,',','.')); ?> | Rp<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($item->total,0,',','.')); ?> |
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
| **Subtotal Produk** |  |  | **Rp<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($pesanan->items->sum('total'),0,',','.')); ?>** |
| **Ongkir**         |  |  | **Rp<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($pesanan->pengiriman->total ?? 0,0,',','.')); ?>** |
| **Total**          |  |  | **Rp<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($pesanan->total,0,',','.')); ?>** |
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::button', ['url' => route('pesanan.invoice', $pesanan->id)]); ?>
Download PDF Invoice
<?php echo $__env->renderComponent(); ?>

Jika ada pertanyaan, balas email ini atau hubungi tim customer service kami.  

Terima kasih telah berbelanja di Apotiku!  
**Apotiku Team**
<?php echo $__env->renderComponent(); ?><?php /**PATH /var/www/html/resources/views/emails/invoice.blade.php ENDPATH**/ ?>