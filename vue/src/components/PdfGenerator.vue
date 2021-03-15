<template>
	<section class="pdf">
		<header class="pdf__header">
			<img src="@/assets/images/header.png" alt="шапка">
			<!-- <div class="pdf__header-text">
				ООО «ТехноНИКОЛЬ-Строительные Системы»<br>
				129110, Москва, ул. Гиляровского, д. 47, стр. 5, эт. 5, пом. I, к. 13<br>
				тел.: +7 495 925 55 75; факс: +7 495 925 81 55<br>
				www.tn.ru, info@tn.ru
			</div>
			<div class="pdf__header-code">
				<img src="@/assets/images/qr.png" alt="qr-код">
			</div> -->
		</header>
		<main class="pdf__content">
			<h1 class="pdf__title">Расчет количества материала TAIKOR для объекта</h1>
			<div class="pdf__data">
				<div>Система: {{system}}</div>
				<div>Площадь: {{area}} <span class="pdf__text-sup">м</span></div>
				<!-- <div>Дата: {{ date }}</div> -->
				<div class="pdf__table-wrapper" v-if="!showWinter">
					<h2 v-if="hasWinter">Летний вариант реализации</h2>
					<MaterialsTable :materials="materials" season="summer" :fullVersion="false" alwaysFullNames="true"></MaterialsTable>
				</div>
				<div class="pdf__table-wrapper" v-if="showWinter">
					<!-- <div v-if="hasWinter" class="html2pdf__page-break"></div> -->
					<h2 v-if="hasWinter">Зимний вариант реализации</h2>
					<MaterialsTable v-if="hasWinter" :materials="materials" season="winter" :fullVersion="false" alwaysFullNames="true"></MaterialsTable>
				</div>
			</div>
		</main>
		<footer class="pdf__footer">
			<div class="pdf__partners">Официальные торговые партнеры <a href="https://contacts.tn.ru" target="_blank">https://contacts.tn.ru</a></div>
			<div class="pdf__contacts">
				<span>Профессиональные консультации</span>
				<span>8 800 600 05 65</span>
				<span>(звонок по России бесплатный)</span>
			</div>
		</footer>
	</section>
</template>

<script>
	import MaterialsTable from './MaterialsTable';
	import html2pdf from 'html2pdf.js';

	export default {
		name: 'PdfGenerator',
		components: {MaterialsTable},
		props: ['materials', 'area', 'system', "hasWinter", "profile", "showWinter"],
		data: () => ({
			date: '',
		}),
		computed: {},
		methods: {
			generate(save) {
				var currentDate = new Date().toLocaleDateString();
				this.date = currentDate;
				return new Promise(resolve => {
					const pdf = html2pdf().from(this.$el)
					pdf.set({
						margin: 15,
						image: {type: 'png' },
						html2canvas: {
							scrollY: 0,
							scrollX: 0,
							scale: 2,
						},
						jsPDF: {}
					});
					pdf.toPdf().then(() => {
						const base64 = btoa(pdf.prop.pdf.output());
						if(save) {
							pdf.save();
						}
						resolve(base64);
					});
				})
			},
		},
	};
</script>

<style lang="scss">
	@font-face {
		font-family: "Open sans";
		src: url("~@/assets/fonts/OpenSans-Regular.woff") format("woff");
		font-weight: 400;
	}

	@font-face {
		font-family: "Open sans";
		src: url("~@/assets/fonts/OpenSans-Bold.woff") format("woff");
		font-weight: 700;
	}

	.pdf {
		font-family: Arial, Helvetica, sans-serif;
		padding: 0 6px 50px 15px;

		&__header {
			display: flex;
			align-items: center;
			// padding: 0 20px;

			img {
				width: 100%;
				// height: 36px;
				// margin-right: 65px;
			}
		}

		// &__header-text {
		// 	width: 325px;
		// 	font-size: 8px;
		// 	margin-right: 20px;
		// }

		// &__header-code {
		// 	width: 44px;
		// 	height: 44px;

		// 	img {
		// 		width: 44px;
		// 		height: 44px;
		// 		margin: 0;
		// 	}
		// }

		&__content {
			margin-top: 140px;
			height: 100%;
		}

		&__title {
			font-size: 20px;
		}

		&__data {
			font-size: 14px;
		}

		&__personal {
			margin-top: 30px;
		}

		&__table-wrapper {
			margin-top: 40px;

			h2 {
				font-size: 16px;
			}

			tr {
				td, th {
					text-align: left;
					padding: 4px;
					width: auto;

					&.left,
					&.center {
						text-align: left;
						padding: 4px;
					}

					&:nth-of-type(2) {
						width: 105px;
					}
				}
			}
		}

		&__footer {
			display: flex;
			flex-flow: column;
		}

		&__contacts,
		&__partners {
			font-size: 14px;

			span {
				display: block;
				margin-top: 10px;
			}
		}

		&__contacts {
			margin-top: 60px;
		}

		&__text-sup {
			display: inline-block;
			position: relative;
			max-width: 10px;

			&::after {
				position: absolute;
				content: '2';
				font-size: 10px;
				top: 4px;
				right: 0;
				transform: translateX(100%) translateY(-50%);
				max-width: 8px;
			}
		}

		// &__date {
		// 	text-align: right;
		// 	margin-top: 400px;
		// 	width: 100%;
		// 	font-size: 14px;
		// }
	}
</style>