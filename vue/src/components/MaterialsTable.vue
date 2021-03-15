<template>
	<div>
		<table>
			<tr>
				<th>ЕКН</th><!--1-->
				<th :colspan="hasComponents ? 1 : 2" class="title">Материал</th><!--2-->
				<th v-if="hasComponents">Компонент</th><!--3-->
				<th v-if="fullVersion">Расход</th><!--4-->
				<th>Количество, кг</th><!--5-->
				<th v-if="fullVersion">Количество банок (ведер), шт</th><!--6-->
				<th>Упаковка (комплект), шт</th><!--7-->
				<th class="final">Общий вес (кратно фасовке), кг</th><!--8-->
			</tr>
			<template v-for="material in renderContent">
				<tr :class="{other: material.componentA.code === void 0}">
					<td>{{ material.componentA.code }}</td><!--1-->
					<td :colspan="material.complex ? 1 : 2" :rowSpan="material.rows" class="left title">
						{{ showFullName(material.fullName) ? material.fullName : material.name }}<template v-if="material.componentA.code === void 0">{{ material.componentA.subname ? ' ' + material.componentA.subname : '*' }}</template>
					</td><!--2-->
					<td v-if="material.complex" :title="material.componentA.description" class="center">A{{ material.componentA.shortDescription && (fullVersion || alwaysFullNames) ? ' ' + material.componentA.shortDescription : '' }}</td><!--3-->
					<td :rowSpan="material.rows" v-if="fullVersion" class="right">
						<div v-if="material.steps.length > 1" style="white-space: nowrap">
							<div v-for="(step, i) in material.steps">
								{{ step.stepName !== void 0 ? step.stepName : "Слой " + (i + 1) }}: {{ numberFormat(step.consumption) }}кг/м<sup>2</sup>
							</div>
							<hr>
							Итого: {{ numberFormat(material.consumption) }}кг/м<sup>2</sup>
						</div>
						<div v-else-if="material.extendedMaterials !== void 0">
							<div v-for="baseMaterial in material.extendedMaterials">
								{{ baseMaterial.rate }}% от {{ baseMaterial.name }}
							</div>
						</div>
						<div v-else-if="material.consumption !== void 0">
							{{ material.consumption }}кг/м<sup>2</sup>
						</div>
					</td><!--4-->
					<td :rowSpan="material.rows" class="right">{{ numberFormat(material.required) }}</td><!--5-->
					<td v-if="fullVersion">{{ material.kit }}</td><!--6-->
					<td :rowSpan="material.rows">{{ material.kit }}</td><!--7-->
					<td class="right final">{{ numberFormat(material.complex ? material.fullMassA : material.fullMass) }}</td><!--8-->
				</tr>
				<tr v-if="material.complex">
					<td>{{ material.componentB.code }}</td><!--1-->
					<td :title="material.componentB.description" class="center">B</td><!--3-->
					<td v-if="fullVersion">{{ material.kit }}</td><!--6-->
					<td class="right final">{{ numberFormat(material.fullMassB) }}</td><!--8-->
				</tr>
			</template>
		</table>
		<div v-if="hasBaseCode" class="table-comment">* ТЕХНОНИКОЛЬ данный материал не производит, материал рассчитан справочно.</div>
	</div>
</template>

<script>
	export default {
		name: 'MaterialsTable',
		props: ['materials', 'fullVersion', 'season', 'alwaysFullNames'],
		data: () => ({}),
		computed: {
			renderContent: function () {
				return this.materials.map(item => {
					console.log(item.data[this.season]);
					const renderMaterial = Object.assign({}, item, item[this.season], item.data[this.season], {
						name: item.data.name,
						fullName: item.data[this.season].componentA.fullName,
						complex: item.data.complex,
						rows: item.data.complex ? 2 : 1,
					});
					if (item.extendedMaterials) {
						renderMaterial.extendedMaterials = item.extendedMaterials.map(extendedMaterial => {
							const material = this.materials.find(material => material.baseCode === extendedMaterial.code);
							return {
								rate: extendedMaterial.rate * 100,
								name: material.data.name,
								fullName: material.data.fullName,
							};
						});
					}
					return renderMaterial;
				});
			},
			hasBaseCode() {
				return this.materials.find(item => item.data.baseCode == null);
			},

			hasComponents: function () {
				return this.materials.findIndex(item => item.data.complex) !== -1;
			}
		},
		methods: {
			numberFormat(value) {
				if (value === void 0) {
					return '';
				}
				return value.toLocaleString('ru-RU', {minimumFractionDigits: 2, maximumFractionDigits: 2});
			},
			showFullName: function (fullName) {
				return fullName != undefined && (this.fullVersion || this.alwaysFullNames == 'true')
			}
		},
	};
</script>

<style scoped lang="scss">
	table {
		border-collapse: collapse;
		color: #333333;
	}

	td, th {
		border: 1px solid #d7d7d7;
		padding: 8px;
		text-align: center;
		width: 13%;

		&:first-of-type {
			font-weight: bold;
		}

	}

	td {
		&.left {
			text-align: left;
			padding-left: 10px;
			padding-right: 10px;
		}

		&.center {
			text-align: center;
		}
	}

	th {
		font-weight: bold;
		font-size: 14px;
		line-height: 19px;
	}

	td hr {
		margin: 0 10px;
	}

	.title {
		width: 35%;
	}

	.other {
		color: #6D6D6D;
	}

	.table-comment {
		color: #000;
		text-align: right;
		padding-top: 5px;
		font-size: 80%;
	}

	.final {
		font-weight: bold;
	}
</style>