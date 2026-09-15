<?php
// Fix products.php
$file = __DIR__ . '/products.php';
$content = file_get_contents($file);
$old = "? \$product['image'] : 'picture/'.\$product['image']";
$new = "? \$product['image'] : (file_exists(__DIR__.'/assets/images/'.\$product['image']) ? 'assets/images/'.\$product['image'] : 'picture/'.\$product['image'])";
$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "products.php fixed!\n";

// Fix index.php
$file2 = __DIR__ . '/index.php';
$content2 = file_get_contents($file2);
$old2 = "? \$product['image'] : 'picture/'.\$product['image']";
$new2 = "? \$product['image'] : (file_exists(__DIR__.'/assets/images/'.\$product['image']) ? 'assets/images/'.\$product['image'] : 'picture/'.\$product['image'])";
$content2 = str_replace($old2, $new2, $content2);
file_put_contents($file2, $content2);
echo "index.php fixed!\n";

// Fix admin/products.php
$file3 = __DIR__ . '/admin/products.php';
$content3 = file_get_contents($file3);
$old3 = "\$thumb_path = '../picture/' . \$product['image']";
$new3 = "\$thumb_path = file_exists('../assets/images/'.\$product['image']) ? '../assets/images/'.\$product['image'] : '../picture/'.\$product['image']";
$content3 = str_replace($old3, $new3, $content3);
file_put_contents($file3, $content3);
echo "admin/products.php fixed!\n";



// Fix admin/edit_product.php preview
$file4 = __DIR__ . '/admin/edit_product.php';
$content4 = file_get_contents($file4);
$old4 = 'src="../picture/<?= htmlspecialchars($product[\'image\']) ?>"';
$new4 = 'src="<?= file_exists(\'../assets/images/\'.$product[\'image\']) ? \'../assets/images/\' : \'../picture/\' ?><?= htmlspecialchars($product[\'image\']) ?>"';
$content4 = str_replace($old4, $new4, $content4);
file_put_contents($file4, $content4);
echo "admin/edit_product.php fixed!\n";

echo "\nDone! All image paths now check both folders.";
?>