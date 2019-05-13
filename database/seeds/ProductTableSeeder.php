<?php

use Illuminate\Database\Seeder;

use App\Models\Product;

class ProductTableSeeder extends Seeder {

	public function run() {
		DB::table('product')->delete();

		$products = [
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'Rice Bran Soap',
				'description' => 'The recommended soap to use for acne-prone
					problem skin, skin allergies, skin infections & skin asthma.
					It has Acapulco plant extract that kills bad bacteria on the
					skin without any negative side effects. It restores normal
					skin condition.',
				'price' => 75.00
			],
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'Collagen Soap',
				'description' => 'Gentle & mild on all skin types, Collagen soap
					made with blueberries & seaweed supplements collagen in the
					skin that gives firmness & strength. It also hydrates & tightens
					skin cells, making it effective in preventing & reducing wrinkle
					lines. It helps maintain a clear complexion.',
				'price' => 75.00
			],
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'L-Glutathione Soap',
				'description' => 'A natural anti-oxidant, glutathione works with
					vitamins E & C to protect the skin from damage caused by constant
					exposure to the environment. Glutathione naturally whitens the
					skin as a side effect. The soap also contains grape seed extract
					& Lipoic acid to keep the skin soft & supple.',
				'price' => 75.00
			],
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'Salad Bar',
				'description' => 'For skin pigmentation, skin discoloration, uneven
					skin tone & sun damage Salad Bar contain Hyalauronic Acid to help
					attract & retain moisture in the skin. It also has Vegetal Bearberry
					that exfoliates skin & reduce melanin production. Anti-oxidant
					properties of grape seed extract strengthen cell membranes & protect
					skin against free radical damage. Vitamins C & E, Virgin & Coconut
					Oil are also present to keep skin even more healthy.',
				'price' => 75.00
			],
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'Strawberry Soap',
				'description' => 'From the strawberry fields of the La Trinidad, this
					pleasant-smelling soap cleanses & helps in preventing skin blemishes.
					With Vitamins C & E, almond oil, papaine & honey, this strawberry
					soap tones skin for a cleaner, healthier & fairer complexion.',
				'price' => 75.00
			],
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'Strawberry with Goat\'s Milk Soap',
				'description' => 'Strawberry soap on one side, Goat\'s milk soap on the
					other, this soap is full of moisturizing ingredients for dry or sensitive
					skin & is especially gentle on baby skin.',
				'price' => 75.00
			],
			[
				'code' => 'MTPHRS'.rand(100000,999999),
				'title' => 'Feminine Wash Soap',
				'description' => 'A non-irritating strawberry wash with tea tree oil &
					organic herbs that deodorize & whiten intimate areas. It promotes normal
					& healthy hygiene for women.',
				'price' => 75.00
			]
		];

		foreach($products as $product) {
			Product::create($product);
		}

	}

}
