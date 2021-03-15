<template>
	<div id="app">
		<section v-if="showMessage !== true && showError !== true" class="calc">
			<header class="calc__header">
				<h1 class="calc__header-title">Технический калькулятор расхода материалов TAIKOR</h1>
				<a href="https://taikor.tn.ru/" target="_blank">
					<img class="calc__header-image" src="img/logo.png" alt="Калькулятор расхода материалов TAIKOR">
				</a>
			</header>
			<div class="calc__body">
				<div class="calc__info">Выберите систему TAIKOR и укажите площадь объекта, чтобы начать расчет</div>
				<div class="calc__fields">
					<div class="calc__field-wrapper calc__field-wrapper--wide">
						<label class="calc__field">
							Система
							<div class="calc__styled_select">
								<select v-model="systemIndex" @change="changeSystem" class="calc__input">
									<option :value="void 0" disabled>Выберите систему</option>
									<option :value="i" v-for="(system, i) in systems">{{ system.name }}</option>
								</select>
							</div>
						</label>
					</div>
					<template v-if="systemSelected && hasRiser">
						<div class="calc__field-wrapper">
							<label class="calc__field">Горизонтальная<br>поверхность ступеней м<sup>2</sup> (подступенок)<input type="text" v-model="area" class="calc__input"></label>
						</div>
						<div class="calc__field-wrapper">
							<label class="calc__field">Вертикальная<br>поверхность ступеней м<sup>2</sup> (проступь)</label>
							<input type="text" v-model="areaRiser" class="calc__input">
						</div>
					</template>
					<template v-else>
						<div class="calc__field-wrapper">
							<label :class="['calc__field', { disabled: !systemSelected}]">Площадь м<sup>2</sup><input type="text" v-model="area" class="calc__input"></label>
						</div>
					</template>
					<div class="calc__field-range-wrapper">
						<label :class="['calc__field-range', { disabled: !systemSelected}]">
							<span>{{ system.ratio_title ? system.ratio_title : 'Коэффициент технологических потерь' }}</span>
							<button v-tooltip="{content: system.ratio_comment}" class="calc__tooltip">?</button>
							<vue-slider class="calc__input-range c-calculator-range-slider"
								v-model="ratio"
								:min="system.ratio_min ? system.ratio_min: 1"
								:max="system.ratio_max ? system.ratio_max: 2"
								:interval="0.05"
								:marks="system.ratio_marks ? system.ratio_marks : [1, 1.1, 1.2, 1.3,  1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 2]"
								tooltip="always"
								tooltip-placement="bottom">
							</vue-slider>
						</label>
						<p v-if="systemSelected">Рекомендуется произвести пробное нанесение на объекте для определения точного расчета расхода.<br>Возможно предоставление бесплатного образца материала для пробного нанесения.</p>
					</div>
				</div>
				<div v-if="systemSelected" class="calc__tables">
					<div class="calc__menu">
						<div class="calc__variant-buttons" v-if="hasWinter && hasModified">
							<input type="checkbox" v-model="showWinter" id="calc-winter-checkbox" v-show="false">
							<label :class="['calc__variant-button calc__variant-button--summer', { active: showSummer}]" for="calc-winter-checkbox">
								<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M2.55 18.54L3.96 19.95L5.76 18.16L4.34 16.74L2.55 18.54ZM10 22.45C10.32 22.45 12 22.45 12 22.45V19.5H10V22.45ZM11 5.49999C9.4087 5.49999 7.88258 6.13213 6.75736 7.25735C5.63214 8.38257 5 9.90869 5 11.5C5 13.0913 5.63214 14.6174 6.75736 15.7426C7.88258 16.8678 9.4087 17.5 11 17.5C12.5913 17.5 14.1174 16.8678 15.2426 15.7426C16.3679 14.6174 17 13.0913 17 11.5C17 8.17999 14.31 5.49999 11 5.49999ZM19 12.5H22V10.5H19V12.5ZM16.24 18.16L18.04 19.95L19.45 18.54L17.66 16.74L16.24 18.16ZM19.45 4.45999L18.04 3.04999L16.24 4.83999L17.66 6.25999L19.45 4.45999ZM12 0.549988H10V3.49999H12V0.549988ZM3 10.5H0V12.5H3V10.5ZM5.76 4.83999L3.96 3.04999L2.55 4.45999L4.34 6.25999L5.76 4.83999Z" fill="#C10725"/>
								</svg>
								<span>Летний вариант реализации (t от +5° до +35°С)</span>
							</label>
							<label :class="['calc__variant-button calc__variant-button--winter', { active: showWinter }]" for="calc-winter-checkbox">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M18.79 11.95L16.46 12.57L14.46 11.44V8.56L16.46 7.43L18.79 8.05L19.31 6.12L17.54 5.65L18 3.88L16.07 3.36L15.45 5.69L13.45 6.82L11 5.38V3.12L12.71 1.41L11.29 0L10 1.29L8.71001 0L7.29001 1.41L9.00001 3.12V5.38L6.50001 6.82L4.50001 5.69L3.92001 3.36L2.00001 3.88L2.47001 5.65L0.700012 6.12L1.22001 8.05L3.55001 7.43L5.55001 8.56V11.45L3.55001 12.58L1.22001 11.96L0.700012 13.89L2.47001 14.36L2.00001 16.12L3.93001 16.64L4.55001 14.31L6.55001 13.18L9.00001 14.62V16.88L7.29001 18.59L8.71001 20L10 18.71L11.29 20L12.7 18.59L11 16.88V14.62L13.5 13.17L15.5 14.3L16.12 16.63L18 16.12L17.53 14.35L19.3 13.88L18.79 11.95ZM7.50001 8.56L10 7.11L12.5 8.56V11.44L10 12.89L7.50001 11.44V8.56Z" fill="#2F80ED"/>
								</svg>
								<span>Зимний вариант реализации (t от -10° до +5°С)</span>
							</label>
						</div>
						<label class="calc__detail"><input type="checkbox" v-model="fullVersion" class="calc__checkbox"> Детализированный вывод</label>
					</div>
					<div v-if="systemSelected" class="calc__tables-list">
						<div v-if="showSummer && hasWinter && hasModified" class="calc__table-title">
							<h2>Летний вариант реализации</h2>
							<!-- <div class="calc__hint-wrapper">
								<div class="calc__hint">
									<div class="calc__hint-content">
										Нанесение материалов при t от +5° до +35°С
									</div>
								</div>
							</div> -->
						</div>
						<MaterialsTable class="calc__table" v-if="showSummer" :materials="systemMaterials" season="summer" :fullVersion="fullVersion" alwaysFullNames="false"></MaterialsTable>
						<div v-if="hasWinter && hasModified && showWinter" class="calc__table-title">
							<h2>Зимний вариант реализации</h2>
							<!-- <div class="calc__hint-wrapper">
								<div class="calc__hint">
									<div class="calc__hint-content">
										Нанесение материалов при t от -10° до +5°С.
									</div>
								</div>
							</div> -->
						</div>
						<MaterialsTable class="calc__table" v-if="hasWinter && hasModified && showWinter" :materials="systemMaterials" season="winter" :fullVersion="fullVersion" alwaysFullNames="false"></MaterialsTable>
						<form @submit="save" class="calc__form">
							<fieldset class="calc__profile-fieldset calc__profile-fieldset--mail">
								<div class="calc__profile-field">
									<label class="calc__profile-label calc__profile-label--mail">
										<input type="checkbox" v-model="profile.sendToOwner" class="calc__profile-checkbox calc__profile-checkbox--mail">
										Получить расчет на почту
									</label>
								</div>
								<div v-if="this.profile.sendToOwner || this.profile.getConsultation" class="calc__profile-field">
									<label for="calc-profile-name" class="calc__profile-label calc__profile-label--fix-width">ФИО<span>*</span></label>
									<input id="calc-profile-name" type="text" v-model="profile.name" required class="calc__profile-input">
								</div>
								<div v-if="profile.sendToOwner" class="calc__profile-field">
									<label for="calc-profile-email" class="calc__profile-label calc__profile-label--fix-width">E-mail<span>*</span></label>
									<input id="calc-profile-email" type="email" v-model="profile.email" required class="calc__profile-input">
								</div>
							</fieldset>
							<fieldset class="calc__profile-fieldset" :class="['calc__profile-fieldset--' + (profile.getConsultation ? 'open' : 'closet')]">
								<div class="calc__profile-field">
									<label class="calc__profile-label">
										<input type="checkbox" v-model="profile.getConsultation" class="calc__profile-checkbox">
										Запросить консультацию
									</label>
								</div>
								<div v-if="profile.getConsultation" class="calc__profile-field">
									<label for="calc-profile-phone" class="calc__profile-label calc__profile-label--fix-width">Телефон<span>*</span></label>
									<input id="calc-profile-phone" type="text" v-model="profile.phone" class="calc__profile-input" required>
								</div>
							</fieldset>
							<div class="calc__profile-field">
								<label v-if="profile.sendToOwner || profile.getConsultation" class="calc__profile-label calc__profile-label--agree">
									<input type="checkbox" class="calc__profile-checkbox" v-model="profile.agree">
									Я согласен с <a href="/politika_obrabotki_personalnyh_dannyh/" target="_blank">Политикой обработки персональных данных</a>
								</label>
							</div>
							<button class="calc__submit" :disabled="!profile.agree && (profile.sendToOwner || profile.getConsultation)" type="submit" onclick="yaCounter31198611.reachGoal('poluchit-raschet')">Получить расчет</button>
							<div style="display:none">
								<PdfGenerator
								:materials="systemMaterials"
								:area="area" :system="systemName"
								:hasWinter="hasWinter && hasModified"
								:showWinter="showWinter"
								:profile="profile"
								ref="pdf"></PdfGenerator>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
		<section v-if="showMessage || showError" class="calc__message">
			<div class="calc__message-wrapper">
				<div class="calc__message-header">
					{{
						showMessage
						? 'Благодарим за обращение в корпорацию ТехноНИКОЛЬ!'
						: 'Возникла ошибка!'
					}}
				</div>
				<div class="calc__message-text">
					{{
						showMessage
						? 'Внимание! Вам на почту было отправлено сообщение, в случае если сообщение не пришло, необходимо проверить папку «Нежелательная почта»."!'
						: 'Ваше сообщение не было отправлено. Повтрите попытку позже или свяжитесь с менеджерами сайта!'
					}}
				</div>
				<button class="calc__message-button" @click="backToCalc()">Вернуться к калькулятору</button>
			</div>
		</section>
	</div>
</template>

<script>
	import Vue from 'vue'
	import 'whatwg-fetch'
	import { VueReCaptcha } from 'vue-recaptcha-v3'
	import VueSlider from 'vue-slider-component';
	import 'vue-slider-component/theme/antd.css';
	import '../vue-slider.scss';
	import { VTooltip } from 'v-tooltip'
	import '../vue-tooltip.scss';
	import {extendedMaterials, materials as allMaterials, systems} from '../bd.json';
	import MaterialsTable from './MaterialsTable';
	import PdfGenerator from './PdfGenerator';

	Vue.directive('tooltip', VTooltip)
	Vue.use(VueReCaptcha, { siteKey: '6LfSta0UAAAAACDnfMrUijawuFGe738Fu8N7LaDV' })

	export default {
		name: 'Taikor',
		components: {MaterialsTable, PdfGenerator, VueSlider},
		data: () => ({
			fullVersion: false,
			showWinter: false,
			area: 100,
			areaRiser: 100,
			ratio: 1.2,
			systemIndex: void 0,
			systems,
			showMessage: false,
			showError: false,
			profile: {
				sendToOwner: true,
				getConsultation: false,
				phone: '',
				name: '',
				email: '',
				agree: false,
			},
		}),
		computed: {
			showSummer: function () {
				return !this.showWinter;
			},
			systemSelected: function () {
				if (this.systemIndex !== void 0) {
					if (this.systems[this.systemIndex].ratio_default) {
						this.ratio = this.systems[this.systemIndex].ratio_default;
					} else {
						this.ratio = 1.2;
					}
				}

				return this.systemIndex !== void 0;
			},
			system: function () {
				const system = this.systems[this.systemIndex];
				return system !== void 0 ? system : {
					name: 'noName',
					materials: [],
				};
			},
			systemName: function () {
				return this.system.name;
			},
			materialsInfo: function () {
				return allMaterials.reduce((list, material) => {
					//Проверка входных данных
					// TODO: move to tests or devEnv
					for (let prop of ['code', 'packing', 'description']) {
						if (material[prop] === void 0) {
							console.warn(`Не найдено обязательное свойство "${prop}" в материале ${material.code} ${material.name}`);
						}
					}
					if (material.name === void 0 && material.root === void 0) {
						console.warn(`Материал не имеет родителя ("root"), но не задано и "name" ${material.code} ${material.name}`);
					}

					//Составление комплексных материалов
					if (material.root === void 0 && material.summer === void 0) {
						const onlySummer = material.hasOwnProperty("onlySummer") ? material.onlySummer : false;
						const data = {
							name: material.name,
							fullName: material.fullName,
							baseCode: material.code,
							onlySummer,
							hasModified: false,
							summer: {
								componentA: material,
								componentB: allMaterials.find(item => item.root === material.code),
							},
							winter: {
								componentA: onlySummer ? void 0 : allMaterials.find(item => item.summer === material.code),
							},
						};
						if (data.summer.componentB !== void 0) {
							data.complex = true;
							data.winter.componentB = onlySummer ? void 0 : allMaterials.find(item => item.summer === data.summer.componentB.code);
						}
						if (data.winter.componentA === void 0 && data.winter.componentB === void 0) {
							data.winter = data.summer;
						} else {
							data.hasModified = true;
						}
						list.push(data);
					}
					return list;
				}, []);
			},
			systemMaterials: function () {
				const calculator = (required, componentA, componentB) => {
					if (componentA === void 0) {
						return void 0;
					}
					let data = {
						required,
						fullMass: required,
					};
					data.packing = componentA.packing;
					if (componentB !== void 0) {
						data.packing += componentB.packing;
					}
					if (data.packing !== void 0) {
						data.kit = Math.ceil(required / data.packing);
						data.fullMassA = data.kit * componentA.packing;
						data.fullMass = data.fullMassA;
						if (componentB !== void 0) {
							data.fullMassB = data.kit * componentB.packing;
							data.fullMass = data.fullMassA + data.fullMassB;
						}
					}
					return data;
				};

				//Группировка слоёв, пополнение информацией о материале из каталога
				const list = this.system.materials.reduce((list, material) => {
					let existItem = void 0;
					if (material.code !== void 0) { //можно сгруппировать по коду
						existItem = list.find(item => item.baseCode === material.code);
						if (existItem !== void 0) {
							existItem.steps.push(material);
						}
					}
					if (existItem === void 0) { //нельзя сгруппировать по коду или такого ещё нет
						let materialData = void 0;
						if (material.code !== void 0) {
							materialData = this.materialsInfo.find(item => item.baseCode === material.code);
						}
						if (materialData === void 0) { // Фейковый материал (не присутствующий в каталоге)
							if (material.name === void 0) {
								material.name = 'Неизвестный материал';
								material.description = '';
							}
							materialData = {
								name: material.name,
								fullName: material.fullName,
								onlySummer: false,
								summer: {
									componentA: material,
									componentB: void 0,
								},
								winter: {
									componentA: material,
									componentB: void 0,
								},
							};
						}
						list.push({
							data: materialData,
							baseCode: material.code,
							useRatio: material.useRatio !== false,
							steps: [material],
							extendedMaterials: void 0,
							consumption: void 0,
						});
					}
					return list;
				}, []);

				//Расчёт необходимых параметров
				list.forEach(item => {
					let hasConsumption = false;
					let consumptionRiser = 0;
					let consumption = 0;
					item.steps.forEach(step => {
						if (step.hasOwnProperty("consumption")) {
							hasConsumption = true;
							if (step.hasOwnProperty("riser") && step.riser === true) { //подступёнок
								consumptionRiser += step.consumption;
							} else {
								consumption += step.consumption;
							}
						} else if(step.hasOwnProperty("extendedMaterials") && item.extendedMaterials === void 0) {
							item.extendedMaterials = step.extendedMaterials;
						}
					});
					if (hasConsumption) {
						item.t1 = consumption;
						item.t2 = consumptionRiser;
						item.consumption = consumption + consumptionRiser;
						let required = consumption * this.area * (item.useRatio ? this.ratio : 1);
						if (this.hasRiser) {
							required += consumptionRiser * this.areaRiser * (item.useRatio ? this.ratio : 1)
						}
						item.summer = calculator(required, item.data.summer.componentA, item.data.summer.componentB);
						item.winter = calculator(required, item.data.winter.componentA, item.data.winter.componentB);
					}
				});

				// Пополнение растворителями
				const codes = list.reduce((list, item) => {
					list.push(item.baseCode);
					return list;
				}, []);
				extendedMaterials.forEach(item => {
					item.for.find(extended => {
						if (!codes.includes(item.code) && codes.includes(extended.code)) {
							const materialData = this.materialsInfo.find(material => material.baseCode === item.code);
							list.push({
								baseCode: item.code,
								steps: [item],
								data: materialData,
								extendedMaterials: item.for,
							});
							codes.push(item.code);
							return true;
						}
						return false;
					});
				});
				//Расчёт разбавителей и прочих примесей
				list.forEach(item => {
					if (item.extendedMaterials !== void 0) {
						const [summerRequired, winterRequired] = item.extendedMaterials.reduce(([summer, winter], item) => {
							const material = list.find(material => material.baseCode === item.code);
							return [summer + material.summer.required * item.rate, winter + material.winter.fullMass * item.rate];
						}, [0, 0]);
						item.summer = calculator(summerRequired, item.data.summer.componentA, item.data.summer.componentB);
						item.winter = calculator(winterRequired, item.data.winter.componentA, item.data.winter.componentB);
					}
				});
				return list;
			},
			hasWinter: function () {
				return this.systemMaterials.every(item => item.data.onlySummer === false);
			},
			hasModified: function () {
				return this.systemMaterials.findIndex(item => item.data.hasModified === true) !== -1;
			},
			hasRiser: function () {
				return this.system.materials.findIndex(item => item.riser === true) !== -1;
			}
		},
		methods: {
			changeSystem: function() {
				if (this.hasWinter == false) {
					this.showWinter = false;
				}
			},

			save: function (event) {
				event.preventDefault();
				if (this.profile.sendToOwner === false && this.profile.sendToOwner === false) {
					this.$refs.pdf.generate(true);
				} else {
					this.$recaptcha("calculator").then(token => {
						this.$refs.pdf.generate(false).then(base64 => {
							let url = '/calculator/send/';

							let jsonData = {
								profile: JSON.stringify(this.profile),
								system: JSON.stringify(this.system),
								materialsInfo: JSON.stringify(this.materialsInfo),
								area: JSON.stringify(this.area),
								ratio: JSON.stringify(this.ratio),
								recaptchaToken: token,
								image: base64,
							};

							let formData = new FormData();

							for (let i in jsonData) {
								formData.append(i, jsonData[i]);
							}

							fetch(url, {
								method: 'POST',
								body: formData,
							}).then((response) => {
								// console.log(response);
								if (response.status == 200) {
									this.showMessage = true;
									yaCounter31198611.reachGoal('raschet-materialov');
								} else {
									this.showError = true;
								}
								this.reload();
								return response.json();
							}).then((data) => {
								console.log(data);
							});
						});
					});
				}
			},
			reload: function () {
				let url = '/';
				fetch(url, {
					method: 'GET',
				}).then((response) => {
					return response.json();
				}).then((data) => {
					console.log(data);
				});
			},
			backToCalc: function () {
				this.showMessage = false;
				this.showError = false;
			}
		},
	};
</script>

<style lang="scss" scoped>
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

	.calc {
		width: 100%;
		margin: 80px auto 0;
		max-width: 999px;
		font-family: "Open Sans", Helvetica, Arial, sans-serif;

		&__header {
			width: 100%;
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
		}

		&__header-title {
			font-size: 25px;
			line-height: 34px;
			color: #c10725;
			margin: 0;
			font-weight: bold;
		}

		&__header-image {
			width: 200px;
			height: 43px;
		}

		&__body {
			margin-top: 37px;
		}

		&__info {
			font-size: 15px;
			line-height: 20px;
			margin-bottom: 37px;
		}

		&__fields {
			display: flex;
			flex-wrap: wrap;
			align-items: flex-end;
			margin: 0 -7px;
		}

		&__field-wrapper {
			margin: 0 7px 30px;
			width: 100%;
			max-width: 236px;

			&--wide {
				max-width: 380px;
			}
		}

		&__field {
			color: #333333;
			font-size: 15px;
			line-height: 20px;
			width: 100%;

			&.disabled {
				pointer-events: none;
				opacity: 0.3;
			}

		}

		&__styled_select {
			position: relative;

			&::after {
				content: '';
				cursor: none;
				pointer-events: none;
				position: absolute;
				bottom: 0;
				right: 1px;
				background: #d7d7d7 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAAICAYAAAAiJnXPAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAABtSURBVHgBnY6xDYAwDAQfo0gpM4KbSJSswAhMhJiIESwmgJ6CNZAQEUlDAyaIayz5/f4HIuLY4QNimdMksRXTZiYxvn41RJ1g5ApIi9H6VTOquibkHt4ONEPx2L3EEICegC4caJt9mZEjW+kPJ4/HLdeNJikDAAAAAElFTkSuQmCC) no-repeat center;
				width: 30px;
				height: 32px;
			}
		}

		&__input {
			background-color: #d7d7d7;
			width: 100%;
			font-size: 13px;
			line-height: 18px;
			color: #333;
			max-height: 32px;
			border: none;
			outline-color: transparent;
			padding: 7px 10px;
			-webkit-appearance: none;
			box-sizing: border-box;
		}

		&__field-range-wrapper {
			width: 100%;
			margin: 0 7px 30px;

			p {
				max-width: 489px;
				font-size: 15px;
			}
		}

		&__field-range {
			color: #333333;
			font-size: 15px;
			line-height: 20px;
			width: 100%;

			&.disabled {
				pointer-events: none;
				opacity: 0.3;
			}
		}

		&__input-range {
			margin: 5px 0 50px;
			max-width: 489px;
		}

		&__variant-buttons {
			margin: 0 -10px;
		}

		&__menu {
			padding-top: 20px;
			padding-bottom: 20px;
			border-bottom: 1px solid #ececec;
			background-color: #fff;
			position: sticky;
			top: 0;
			width: 100%;
			max-width: 999px;
			z-index: 1;
		}

		&__variant-button {
			display: inline-block;
			border: 1px solid #c10725;
			color: #c10725;
			border-radius: 70px;
			margin: 0 10px;
			font-size: 15px;
			line-height: 39px;
			padding: 0 12px;
			transition: 0.2s;
			cursor: pointer;

			svg {
				margin-right: 11px;
				vertical-align: middle;
			}

			&::after {
				display: inline-block;
				content: "";
				width: 20px;
				height: 20px;
				background: url("~@/assets/images/close.png") no-repeat;
				vertical-align: middle;
				margin-left: 6px;
				opacity: 0;
			}

			&.active::after {
				opacity: 1;
			}

			&--summer {
				&:hover, &.active {
					background-color: #c10725;
					color: #fff;

					svg {
						path {
							fill: #fff;
						}
					}
				}
			}

			&--winter {
				border-color: #2f80eD;
				color: #2f80eD;
				&:hover, &.active {
					background-color: #2f80eD;
					color: #fff;

					svg {
						path {
							fill: #fff;
						}
					}
				}
			}
		}

		&__detail {
			display: block;
			align-items: center;
			margin-top: 5px;
			text-align: right;
			cursor: pointer;
		}

		&__tables {
			position: relative;
		}

		&__tables-list {
			padding-top: 30px;
		}

		&__table-title {
			margin: 0 0 20px;
			color: #333333;
			display: flex;
			align-items: center;

			h2 {
				margin: 0;
				font-weight: bold;
				font-size: 18px;
				line-height: 26px;
			}
		}

		&__table {
			margin-bottom: 30px;
		}

		&__tooltip {
			border-radius: 50%;
			width: 16px;
			line-height: 16px;
			padding: 0;
			text-align: center;
			font-weight: bold;
			font-size: 12px;
			color: #fff;
			border: none;
			background: #c10725;
			display: inline-block;
			margin-left: 5px;

			&:focus {
				outline: none;
			}
		}

		&__hint {
			display: none;
			position: absolute;
			max-width: 378px;
			top: -110px;
			right: -27px;
			z-index: 1;
		}

		&__hint-content {
			position: relative;
			font-weight: normal;
			padding: 10px 20px 20px;
			font-size: 12px;
			line-height: 16px;
			color: #333333;
			background-color: #fff;
			box-shadow: 0 4px 14px rgba(0, 0, 0, 0.16);

			&::after {
				position: absolute;
				content: '';
				width: 0;
				height: 0;
				bottom: -13px;
				left: 30px;
				border-style: solid;
				border-width: 13px 10px 0 10px;
				border-color: #fff transparent transparent transparent;
			}
		}

		&__hint-wrapper {
			position: relative;
			display: flex;
			align-items: center;
			height: 18px;
			line-height: 1;
			width: 18px;
			margin-left: 9px;

			&::after {
				content: '';
				cursor: pointer;
				position: absolute;
				width: 18px;
				height: 18px;
				transition: 0.2s;
				top: 50%;
				background: url('../assets/images/question-mark.svg') no-repeat center;
				left: 50%;
				transform: translateX(-50%) translateY(-50%);
			}

			&:hover {
				&::after {
					opacity: 0.6;
				}

				.calc__hint {
					display: block;
				}
			}

			.calc__hint {
				min-width: 176px;
				right: -126px;
				top: -80px;
			}

			.calc__hint-content {
				padding: 10px 16px 20px;
			}
		}

		&__checkbox {
			width: 13px;
			height: 13px;
			margin: 0 10px 0 0;
		}

		&__form {
			display: flex;
			flex-flow: column;
			font-size: 15px;
			line-height: 20px;
		}

		&__profile-fieldset {
			border: none;
			padding: 0;
			margin: 0 0 18px;

			&--closet {
				margin-bottom: 0;
			}

			&--mail {
				margin-bottom: 32px;
			}
		}

		&__profile-field {
			margin-bottom: 12px;
		}

		&__profile-label {
			cursor: pointer;
			display: inline-block;

			&--mail {
				font-weight: bold;
				font-size: 16px;
				margin-bottom: 8px;
			}

			&--fix-width {
				width: 92px;
			}

			&--agree {
				a {
					color: #c10725;
					transition: 0.2s;
					text-decoration: none;
					padding-bottom: 1px;
					border-bottom: 1px solid #c10725;

					&:hover {
						opacity: 0.6;
						border-color: transparent;
					}
				}
			}

			span {
				margin-left: 3px;
				color: #c10725;
			}
		}

		&__profile-input {
			height: 32px;
			border: 0;
			background: #D7D7D7;
			width: 226px;
		}

		&__profile-checkbox {
			cursor: pointer;
			width: 13px;
			height: 13px;
			margin: 0 5px 0 0;

			&--mail:checked {
				display: none;
			}
		}

		&__submit {
			margin-top: 30px;
			max-width: 183px;
			padding: 9px 12px 8px;
			background-color: #c10725;
			border: 1px solid #c10725;
			cursor: pointer;
			transition: 0.2s;
			font-size: 15px;
			line-height: 20px;
			color: #fff;

			&:hover {
				background-color: transparent;
				color: #c10725;
			}

			&[disabled] {
				opacity: 0.6;
				pointer-events: none;
			}
		}

		&__message {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 100%;
			max-width: 1000px;
			background-color: white;
			border-radius: 5px;
			font-family: "Open Sans", Helvetica, Arial, sans-serif;
		}

		&__message-wrapper {
			display: flex;
			flex-flow: column;
			align-items: center;
		}

		&__message-header {
			width: 100%;
			text-align: center;
			font-size: 24px;
			padding-bottom: 20px;
			border-bottom: 2px solid #e41f25;;
		}

		&__message-text {
			margin-top: 20px;
			font-size: 16px;
			text-align: center;
			max-width: 650px;
		}

		&__message-button {
			margin-top: 20px;
			font-family: "Open Sans", Helvetica, Arial, sans-serif;
			font-size: 16px;
			color: #fff;
			border: 1px solid #e41f25;
			background: #e41f25;
			border-radius: 2px;
			padding: 7px 16px 7px 16px;
			transition: all .3s;
			position: static;
			height: auto;
			width: auto;
			display: inline-block;
			cursor: pointer;

			&:hover {
				border-color: #e41f25;
				color: #e41f25;
				background: #fff;
			}
		}
	}
</style>