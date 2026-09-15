<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>Populating Database with Complete Products & Images...</h2>";

$products_data = [
    // Category 1: চাল ও ডাল
    ['name' => 'খাঁটি চিনিগুঁড়া চাল', 'category_id' => 1, 'price' => 140.00, 'unit' => 'kg', 'stock' => 50, 'image' => 'chinigura.jpg', 'is_featured' => 1, 'description' => 'দিনাজপুরের সেরা মানের সুগন্ধি চিনিগুঁড়া চাল। পোলাও, বিরিয়ানি বা পায়েস তৈরির জন্য দারুণ।'],
    ['name' => 'ক্যাটারীভোগ চাল', 'category_id' => 1, 'price' => 95.00, 'unit' => 'kg', 'stock' => 40, 'image' => 'cataribog.jpg', 'is_featured' => 0, 'description' => 'দিনাজপুরের খাঁটি ক্যাটরীভোগ সুগন্ধি সরু চাল। প্রতিদিনের খাওয়ার জন্য সুস্বাদু ও পুষ্টিকর।'],
    ['name' => 'মিনিকেট প্রিমিয়াম চাল', 'category_id' => 1, 'price' => 78.00, 'unit' => 'kg', 'stock' => 100, 'image' => 'minikate1.jpg', 'is_featured' => 1, 'description' => '১০০% অটো রাইস মিলের ঝরঝরে ও পুষ্টিকর প্রিমিয়াম মিনিকেট চাল।'],
    ['name' => 'নাজিরশাইল সরু চাল', 'category_id' => 1, 'price' => 85.00, 'unit' => 'kg', 'stock' => 60, 'image' => 'nazirshailcal.jpg', 'is_featured' => 0, 'description' => 'আসল পুরনো নাজিরশাইল চাল। ভাত হয় ধবধবে সাদা ও ঝরঝরে।'],
    ['name' => 'খাঁটি দেশি মসুর ডাল', 'category_id' => 1, 'price' => 135.00, 'unit' => 'kg', 'stock' => 45, 'image' => 'mosurdal.jpg', 'is_featured' => 1, 'description' => 'গ্রামের কৃষকের জমি থেকে সংগৃহীত ছোট দানার খাঁটি দেশি মসুর ডাল।'],
    ['name' => 'প্রিমিয়াম মুগ ডাল', 'category_id' => 1, 'price' => 160.00, 'unit' => 'kg', 'stock' => 30, 'image' => 'mugdal.jpg', 'is_featured' => 0, 'description' => 'সোনালী রঙের ভাজা দেশি সোনা মুগ ডাল। চমৎকার সুবাস ও স্বাদ।'],
    ['name' => 'দেশি খেসারি ডাল', 'category_id' => 1, 'price' => 90.00, 'unit' => 'kg', 'stock' => 25, 'image' => 'khesaridal.jpg', 'is_featured' => 0, 'description' => 'খাটি ও পরিষ্কার খেসারি ডাল। পেঁয়াজু ও ডাল পুরির জন্য সেরা।'],
    ['name' => 'বুটের ডাল (ছোলার ডাল)', 'category_id' => 1, 'price' => 110.00, 'unit' => 'kg', 'stock' => 35, 'image' => 'buterdal2.jpg', 'is_featured' => 0, 'description' => 'উন্নত জাতের বুটের ডাল। হাড়ের মাংস বা হালুয়া রান্নায় দারুণ জমে।'],

    // Category 2: শাকসবজি
    ['name' => 'বিষমুক্ত তাজা করলা', 'category_id' => 2, 'price' => 60.00, 'unit' => 'kg', 'stock' => 30, 'image' => 'bitter-gourd.jpg', 'is_featured' => 0, 'description' => 'আমাদের নিজস্ব জমিতে উৎপাদিত সম্পূর্ণ বিষমুক্ত তাজা করলা।'],
    ['name' => 'নতুন গোল আলু', 'category_id' => 2, 'price' => 45.00, 'unit' => 'kg', 'stock' => 150, 'image' => 'alu.jpg', 'is_featured' => 1, 'description' => 'বগুড়ার তাজা দেশি নতুন গোল আলু। তরকারি ও ভাজির জন্য সেরা।'],
    ['name' => 'তাজা বেগুন', 'category_id' => 2, 'price' => 55.00, 'unit' => 'kg', 'stock' => 40, 'image' => 'begun.jpg', 'is_featured' => 0, 'description' => 'ক্ষেত থেকে তোলা কচি নরম লম্বা ও গোল বেগুন।'],
    ['name' => 'মিষ্টি কচি লাউ', 'category_id' => 2, 'price' => 65.00, 'unit' => 'piece', 'stock' => 20, 'image' => 'lau.jpg', 'is_featured' => 1, 'description' => 'সকালের কচি মিষ্টি লাউ। রুই মাছের ঝোল বা ডাল লাউয়ের জন্য দারুণ।'],
    ['name' => 'লাল টমেটো', 'category_id' => 2, 'price' => 70.00, 'unit' => 'kg', 'stock' => 50, 'image' => 'tometo.jpg', 'is_featured' => 0, 'description' => 'গাছপাকা তাজা লাল টমেটো। সালাদ ও রান্নার স্বাদ বাড়ায়।'],
    ['name' => 'কচি পাতাকপি', 'category_id' => 2, 'price' => 40.00, 'unit' => 'piece', 'stock' => 35, 'image' => 'cabbage.jpg', 'is_featured' => 0, 'description' => 'তাজা ও কচি সবুজ পাতাকপি। সম্পূর্ণ ক্ষতিকারক কিটনাশক মুক্ত।'],
    ['name' => 'তাজা ফুলকপি', 'category_id' => 2, 'price' => 45.00, 'unit' => 'piece', 'stock' => 30, 'image' => 'cauliflower1.jpg', 'is_featured' => 0, 'description' => 'সাদা ধবধবে বড় আকারের তাজা ফুলকপি।'],
    ['name' => 'দেশি গাজর', 'category_id' => 2, 'price' => 60.00, 'unit' => 'kg', 'stock' => 25, 'image' => 'gazor.jpg', 'is_featured' => 0, 'description' => 'মিষ্টি ও রসালো লাল গাজর। হালুয়া ও সালাদের জন্য পুষ্টিকর।'],
    ['name' => 'সবুজ শসা', 'category_id' => 2, 'price' => 50.00, 'unit' => 'kg', 'stock' => 40, 'image' => 'sosha.jpg', 'is_featured' => 0, 'description' => 'তাজা কচি দেশি শসা। পেটের স্বাস্থ্য রক্ষায় ও সালাদে অনন্য।'],
    ['name' => 'তাজা পালং শাক', 'category_id' => 2, 'price' => 30.00, 'unit' => 'bunch', 'stock' => 50, 'image' => 'palongshak.jpg', 'is_featured' => 0, 'description' => 'সবুজ পাতাযুক্ত সতেজ কচি পালং শাক। আইরন ও ভিটামিনে সমৃদ্ধ।'],
    ['name' => 'কচি ঢ্যাঁড়শ', 'category_id' => 2, 'price' => 50.00, 'unit' => 'kg', 'stock' => 30, 'image' => 'ladies_finger.jpg', 'is_featured' => 0, 'description' => 'নরম ও কচি সবুজ ঢ্যাঁড়শ/বেন্ডি।'],
    ['name' => 'ঝাল কাঁচা মরিচ', 'category_id' => 2, 'price' => 120.00, 'unit' => 'kg', 'stock' => 25, 'image' => 'kaca_moris.jpg', 'is_featured' => 0, 'description' => 'ক্ষেত থেকে তোলা সুগন্ধি ও তীব্র ঝাল কাঁচা মরিচ।'],
    ['name' => 'দেশি আদা', 'category_id' => 2, 'price' => 180.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'ada.jpg', 'is_featured' => 0, 'description' => 'ঝাঁঝালো ও সুগন্ধযুক্ত খাঁটি দেশি আদা।'],

    // Category 3: ফলমূল
    ['name' => 'রাজশাহী হিমসাগর আম', 'category_id' => 3, 'price' => 120.00, 'unit' => 'kg', 'stock' => 100, 'image' => 'himsagor.jpg', 'is_featured' => 1, 'description' => 'রাজশাহীর বিখ্যাত ফরমালিন মুক্ত মিষ্টি হিমসাগর আম।'],
    ['name' => 'মিষ্টি ফজলি আম', 'category_id' => 3, 'price' => 100.00, 'unit' => 'kg', 'stock' => 80, 'image' => 'fazliam.jpg', 'is_featured' => 0, 'description' => 'চাঁপাই নবাবগঞ্জের সুস্বাদু রসালো বড় ফজলি আম।'],
    ['name' => 'চাঁপাই ল্যাংড়া আম', 'category_id' => 3, 'price' => 110.00, 'unit' => 'kg', 'stock' => 90, 'image' => 'langra_am.jpg', 'is_featured' => 0, 'description' => 'চমৎকার সুবাস ও অনন্য স্বাদের ল্যাংড়া আম।'],
    ['name' => 'গাছপাকা কচি কাঁঠাল', 'category_id' => 3, 'price' => 150.00, 'unit' => 'piece', 'stock' => 15, 'image' => 'kathal.jpg', 'is_featured' => 0, 'description' => 'জাতীয় ফল মিষ্টি রসে ভরা গাছপাকা কাঁঠাল।'],
    ['name' => 'দিনাজপুরের সেরা লিচু', 'category_id' => 3, 'price' => 380.00, 'unit' => '100-pcs', 'stock' => 50, 'image' => 'licu.jpg', 'is_featured' => 1, 'description' => 'দিনাজপুরের বেদানা ও বোম্বাই সেরা মিষ্টি লিচু।'],
    ['name' => 'শ্রীমঙ্গলের মিষ্টি আনারস', 'category_id' => 3, 'price' => 60.00, 'unit' => 'piece', 'stock' => 40, 'image' => 'anaros.jpg', 'is_featured' => 0, 'description' => 'শ্রীমঙ্গলের পানের ছড়ার কচি ও রসালো মিষ্টি আনারস।'],
    ['name' => 'মিষ্টি বাঙ্গি', 'category_id' => 3, 'price' => 90.00, 'unit' => 'piece', 'stock' => 20, 'image' => 'bangi.jpg', 'is_featured' => 0, 'description' => 'গরমের স্বস্তিদায়ক তাজা ও মিষ্টি বাঙ্গি ফল।'],
    ['name' => 'লাল সুস্বাদু তরমুজ', 'category_id' => 3, 'price' => 250.00, 'unit' => 'piece', 'stock' => 25, 'image' => 'tormuj.jpg', 'is_featured' => 1, 'description' => 'বরিশালের লাল টুকটুকে মিষ্টি পতি তরমুজ।'],
    ['name' => 'মিষ্টি পেঁপে', 'category_id' => 3, 'price' => 60.00, 'unit' => 'kg', 'stock' => 35, 'image' => 'papya.jpg', 'is_featured' => 0, 'description' => 'গাছপাকা হলুদ মিষ্টি পেঁপে। পেটের জন্য অতি উপকারী।'],
    ['name' => 'দেশি পেঁয়ারা', 'category_id' => 3, 'price' => 80.00, 'unit' => 'kg', 'stock' => 45, 'image' => 'peyara.jpg', 'is_featured' => 0, 'description' => 'ঝালকাঠির ভাসমান হাটের তাজা কচি কাজী পেঁয়ারা।'],
    ['name' => 'সবরি কলা', 'category_id' => 3, 'price' => 90.00, 'unit' => 'dozen', 'stock' => 60, 'image' => 'sabrikola.jpg', 'is_featured' => 0, 'description' => 'নরসিংদীর বিখ্যাত মিষ্টি সবরি কলা।'],
    ['name' => 'রসালো লাল ডালিম', 'category_id' => 3, 'price' => 320.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'dalim.jpg', 'is_featured' => 0, 'description' => 'মিষ্টি দানাদার তাজা ডালিম ফল।'],

    // Category 4: মধু ও ঘি
    ['name' => 'সুন্দরবনের খাঁটি মধু', 'category_id' => 4, 'price' => 850.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'honey.jpg', 'is_featured' => 1, 'description' => 'সরাসরি সুন্দরবনের চাক থেকে কাটা ১০০% বিশুদ্ধ মধু।'],
    ['name' => 'গাওয়া ঘি', 'category_id' => 4, 'price' => 1200.00, 'unit' => 'kg', 'stock' => 10, 'image' => 'ghee.jpg', 'is_featured' => 1, 'description' => 'গ্রামের ঘানি ভাঙা খাঁটি সরিষার তেল ও গরুর দুধ থেকে তৈরি গাওয়া ঘি।'],
    ['name' => 'কালোজিরা ফুলের মধু', 'category_id' => 4, 'price' => 950.00, 'unit' => 'kg', 'stock' => 15, 'image' => 'kalojirafulermodu.jpg', 'is_featured' => 0, 'description' => 'প্রাকৃতিক কালোজিরা ফুলের ১০০% খাঁটি প্রাকৃতিক মধু।'],
    ['name' => 'লিচু ফুলের খাঁটি মধু', 'category_id' => 4, 'price' => 750.00, 'unit' => 'kg', 'stock' => 18, 'image' => 'licufulermodu.jpg', 'is_featured' => 0, 'description' => 'দিনাজপুরের লিচু বাগান থেকে প্রাকৃতিক উপায়ে সংগৃহীত মধু।'],
    ['name' => 'সরিষা ফুলের মধু', 'category_id' => 4, 'price' => 650.00, 'unit' => 'kg', 'stock' => 25, 'image' => 'sorisafulermodu.jpg', 'is_featured' => 0, 'description' => 'শীতের সরিষা ক্ষেতের প্রাকৃতিক সুস্বাদু ঘন মধু।'],

    // Category 5: দেশি হাঁস-মুরগি
    ['name' => 'খাঁটি দেশি মুরগি', 'category_id' => 5, 'price' => 550.00, 'unit' => 'kg', 'stock' => 25, 'image' => 'deshimurgi.jpg', 'is_featured' => 1, 'description' => 'মুক্তভাবে চরে বেড়ানো খাঁটি দেশি মুরগি। স্বাদে ও গুণে অতুলনীয়।'],
    ['name' => 'দেশি কক মুরগি', 'category_id' => 5, 'price' => 420.00, 'unit' => 'kg', 'stock' => 30, 'image' => 'deshicock.jpg', 'is_featured' => 0, 'description' => 'গ্রামের তাজা কক মুরগি। ঝোল ও কষানোর জন্য চমৎকার।'],
    ['name' => 'পুকুরের তাজা দেশি হাঁস', 'category_id' => 5, 'price' => 650.00, 'unit' => 'piece', 'stock' => 15, 'image' => 'deshihas1.jpg', 'is_featured' => 1, 'description' => 'গ্রামের বিল ও পুকুরে চরে খাওয়া তাজা স্বাস্থ্যবান দেশি হাঁস।'],
    ['name' => 'চিনাহাঁস', 'category_id' => 5, 'price' => 750.00, 'unit' => 'piece', 'stock' => 10, 'image' => 'china_hash.jpg', 'is_featured' => 0, 'description' => 'বড় সাইজের সুস্বাদু চিনাহাঁস। ভূনা ও ঝোলের জন্য সেরা।'],
    ['name' => 'বড় রাজহাঁস', 'category_id' => 5, 'price' => 1200.00, 'unit' => 'piece', 'stock' => 8, 'image' => 'rajhas.jpg', 'is_featured' => 0, 'description' => 'গ্রামের তাজা বড় রাজহাঁস। মাংস অত্যন্ত সুস্বাদু।'],
    ['name' => 'কোয়েল পাখি (৪ পিস)', 'category_id' => 5, 'price' => 220.00, 'unit' => '4-pcs', 'stock' => 20, 'image' => 'koelpakhi.jpg', 'is_featured' => 0, 'description' => 'ফার্মের প্রস্তুতকৃত ৪ টি কচি নরম কোয়েল পাখি।'],
    ['name' => 'দেশি কবুতর জোড়া', 'category_id' => 5, 'price' => 350.00, 'unit' => 'pair', 'stock' => 12, 'image' => 'pegion.jpg', 'is_featured' => 0, 'description' => 'রোগীদের পথ্য ও পুষ্টির জন্য ১ জোড়া কচি দেশি কবুতর।'],

    // Category 6: ডিম
    ['name' => 'দেশি মুরগির ডিম (১২ টি)', 'category_id' => 6, 'price' => 180.00, 'unit' => 'dozen', 'stock' => 40, 'image' => 'deshimurgirdim.jpg', 'is_featured' => 1, 'description' => 'গ্রামের লালচে কুসুমযুক্ত ১০০% খাঁটি দেশি মুরগির ডিম।'],
    ['name' => 'তাজা হাঁসের ডিম (১২ টি)', 'category_id' => 6, 'price' => 195.00, 'unit' => 'dozen', 'stock' => 35, 'image' => 'haserdim.jpg', 'is_featured' => 1, 'description' => 'তাজা ও পুষ্টিকর বড় সাইজের হাঁসের ডিম।'],
    ['name' => 'ফার্মের লাল ডিম (১২ টি)', 'category_id' => 6, 'price' => 145.00, 'unit' => 'dozen', 'stock' => 100, 'image' => 'murgirdim.jpg', 'is_featured' => 0, 'description' => 'প্রতিদিনের পুষ্টি পূরণে প্রোটিন সমৃদ্ধ ফার্মের লাল ডিম।'],
    ['name' => 'রাজহাঁসের ডিম (৪ টি)', 'category_id' => 6, 'price' => 300.00, 'unit' => '4-pcs', 'stock' => 15, 'image' => 'rajhaserdim.jpg', 'is_featured' => 0, 'description' => 'বড় আকারের অত্যন্ত সুস্বাদু রাজহাঁসের ডিম।'],

    // Category 7: গরু-ছাগলের মাংস
    ['name' => 'খাঁটি গরুর মাংস (হাড়সহ)', 'category_id' => 7, 'price' => 780.00, 'unit' => 'kg', 'stock' => 30, 'image' => 'harsoho_mangso.jpg', 'is_featured' => 1, 'description' => 'প্রতিদিন সকালে জবাই করা ১০০% খাঁটি ও তাজা গরুর মাংস।'],
    ['name' => 'তাজা গরুর কলিজা', 'category_id' => 7, 'price' => 750.00, 'unit' => 'kg', 'stock' => 15, 'image' => 'gorur_koliza.jpg', 'is_featured' => 0, 'description' => 'তাজা গরুর পুষ্টিকর লাল কলিজা।'],
    ['name' => 'গরুর পায়া / নেহারি', 'category_id' => 7, 'price' => 450.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'beefshankcowlegbone).jpg', 'is_featured' => 0, 'description' => 'নেহারি ও পায়া তৈরির জন্য পরিষ্কার করা গরুর পা।'],
    ['name' => 'খাসির খাঁটি মাংস', 'category_id' => 7, 'price' => 1100.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'mutton.jpg', 'is_featured' => 1, 'description' => 'কচি খাসির নরম ও সুস্বাদু খাঁটি চর্বিযুক্ত মাংস।'],
    ['name' => 'পাঠার মাংস', 'category_id' => 7, 'price' => 950.00, 'unit' => 'kg', 'stock' => 15, 'image' => 'ram_meat.jpg', 'is_featured' => 0, 'description' => 'দেশি পাঠার তাজা চর্বিমুক্ত লাল মাংস।'],

    // Category 8: দেশি মাছ
    ['name' => 'পুকুরের তাজা রুই মাছ', 'category_id' => 8, 'price' => 350.00, 'unit' => 'kg', 'stock' => 25, 'image' => 'rui.jpg', 'is_featured' => 1, 'description' => 'আমাদের নিজস্ব পুকুরের কচি বড় সাইজের মিষ্টি রুই মাছ।'],
    ['name' => 'বড় কাতলা মাছ', 'category_id' => 8, 'price' => 380.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'katla.jpg', 'is_featured' => 0, 'description' => 'পুকুরের মিষ্টি পানির ৩-৪ কেজি সাইজের কাতলা মাছ।'],
    ['name' => 'মৃগেল মাছ', 'category_id' => 8, 'price' => 280.00, 'unit' => 'kg', 'stock' => 30, 'image' => 'mrigel.jpg', 'is_featured' => 0, 'description' => 'তাজা মৃগেল মাছ। ঝোল বা কালিয়া রান্নার জন্য পারফেক্ট।'],
    ['name' => 'দেশি মাগুর মাছ', 'category_id' => 8, 'price' => 650.00, 'unit' => 'kg', 'stock' => 15, 'image' => 'magur.jpg', 'is_featured' => 1, 'description' => 'জীবন্ত কাদা পানির পুষ্টিকর দেশি মাগুর মাছ।'],
    ['name' => 'তাজা টেংরা মাছ', 'category_id' => 8, 'price' => 550.00, 'unit' => 'kg', 'stock' => 18, 'image' => 'tengra.jpg', 'is_featured' => 0, 'description' => 'নদীর ও বিলের তাজা ছোট টেংরা মাছ।'],
    ['name' => 'পাঙ্গাস মাছ', 'category_id' => 8, 'price' => 180.00, 'unit' => 'kg', 'stock' => 50, 'image' => 'pangasmas.jpg', 'is_featured' => 0, 'description' => 'তাজা বড় পাঙ্গাস মাছ।'],
    ['name' => 'তেলাপিয়া মাছ', 'category_id' => 8, 'price' => 200.00, 'unit' => 'kg', 'stock' => 40, 'image' => 'telapia.jpg', 'is_featured' => 0, 'description' => 'পুকুরের তাজা মাঝারি সাইজের তেলাপিয়া মাছ।'],

    // Category 9: ডেইরি পণ্য
    ['name' => 'খাঁটি গরুর দুধ', 'category_id' => 9, 'price' => 90.00, 'unit' => 'liter', 'stock' => 50, 'image' => 'khatigorurdud.jpg', 'is_featured' => 1, 'description' => 'ফার্মের শতভাগ খাঁটি ও পানি মুক্ত তরল গরুর দুধ।'],
    ['name' => 'বগুড়ার ঐতিহ্যবাহী দই', 'category_id' => 9, 'price' => 260.00, 'unit' => 'pot', 'stock' => 30, 'image' => 'bogurardoi.jpg', 'is_featured' => 1, 'description' => 'বগুড়ার বিখ্যাত সরা দই। ঘন, মিষ্টি ও সুস্বাদু।'],
    ['name' => 'মিষ্টি দই', 'category_id' => 9, 'price' => 240.00, 'unit' => 'pot', 'stock' => 25, 'image' => 'mistidoi.jpg', 'is_featured' => 0, 'description' => 'খাঁটি গরুর দুধ দিয়ে তৈরি ঐতিহ্যবাহী মিষ্টি দই।'],
    ['name' => 'দুধের সর ও মালাই', 'category_id' => 9, 'price' => 350.00, 'unit' => 'kg', 'stock' => 10, 'image' => 'dudermalai.jpg', 'is_featured' => 0, 'description' => 'ঘন দুধের সুস্বাদু সর ও কড়কড়ে মালাই।'],
    ['name' => 'ঘরের তৈরি মাখন', 'category_id' => 9, 'price' => 800.00, 'unit' => 'kg', 'stock' => 12, 'image' => 'makhon.jpg', 'is_featured' => 0, 'description' => 'গরুর দুধের ঘোল থেকে তোলা ১০০% প্রাকৃতিক সাদা মাখন।'],
    ['name' => 'খাঁটি ছানার পনির', 'category_id' => 9, 'price' => 700.00, 'unit' => 'kg', 'stock' => 15, 'image' => 'ponir.jpg', 'is_featured' => 0, 'description' => 'তাজা দুধের ছানা দিয়ে তৈরি নরম পনির।'],
    ['name' => 'খাঁটি ছানার মিষ্টি', 'category_id' => 9, 'price' => 380.00, 'unit' => 'kg', 'stock' => 20, 'image' => 'sanamisti.jpg', 'is_featured' => 0, 'description' => 'খাঁটি ছানা দিয়ে তৈরি জিভে জল আনা রসালো মিষ্টি।']
];

try {
    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, unit, stock, image, is_featured, description, farmer_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                           ON DUPLICATE KEY UPDATE 
                           category_id = VALUES(category_id),
                           price = VALUES(price),
                           unit = VALUES(unit),
                           stock = VALUES(stock),
                           image = VALUES(image),
                           is_featured = VALUES(is_featured),
                           description = VALUES(description)");
    
    $inserted = 0;
    foreach($products_data as $p) {
        $stmt->execute([
            $p['name'],
            $p['category_id'],
            $p['price'],
            $p['unit'],
            $p['stock'],
            $p['image'],
            $p['is_featured'],
            $p['description']
        ]);
        $inserted++;
    }
    
    echo "<h3>Success! Inserted/Updated {$inserted} products across all 9 categories!</h3>";
} catch(Exception $e) {
    echo "<h3 style='color:red;'>Error: " . $e->getMessage() . "</h3>";
}
