<section class="container">

	<div id="product_container" class="block_sections">

		{foreach from=$products item=bsection}

			<div class="block_section_container">

				<div class="pos_title">

					<h2>

						<a href="{if $bsection.id == 3 || $bsection.id == 4}/sitemap{else}{$bsection.link}{/if}">

							{if $bsection.id == 8}
								Saldatrici INVERTER MMA
							{elseif $bsection.id == 2}
								Saldatrici TIG (AC / DC)
							{elseif $bsection.id == 3}
								Accessori per saldatura e materiali di consumo
							{elseif $bsection.id == 4}
								Offerte complementari alla Saldatura
							{elseif $bsection.id == 5}
								Saldatrici MIG MAG
							{else}
								{$bsection.title}
							{/if}							

						</a>

					</h2>

					<div class="row swiper brandSwiper" id="brandSwiper-{$bsection@iteration}">

						<div class="swiper-wrapper">

							{foreach from=$bsection.products item="product"}

								{include file="catalog/_partials/miniatures/old-product.tpl" product=$product productClasses="swiper-slide"}

							{/foreach}

						</div>

					</div>

					<div class="block-affichertout">

						<a href="{if $bsection.id == 3 || $bsection.id == 4}/sitemap{else}{$bsection.link}{/if}" title="{$bsection.title}" class="affichertout">
							{if $bsection.id == 8}
								Visualizza l'intera gamma INVERTER MMA
							{elseif $bsection.id == 2}
								Visualizza l'intera gamma TIG (AC / DC)
							{elseif $bsection.id == 3}
								Visualizza tutti gli accessori e materiali di consumo per saldatura
							{elseif $bsection.id == 4}
								Visualizza tutte le famiglie di prodotto
							{elseif $bsection.id == 5}
								Visualizza l'intera gamma MIG MAG
							{else}
								{$bsection.title}
							{/if}
						</a>

					</div>

				</div>

			</div>

		{/foreach}

	</div>

</section>





<script></script>
