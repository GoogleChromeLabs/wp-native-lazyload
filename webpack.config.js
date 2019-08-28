/**
 * Webpack config.
 *
 * Site Kit by Google, Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

const mode = process.env.NODE_ENV === 'production' ? 'production' : 'development';

module.exports = {
	mode,
	entry: {
		lazyload: './assets/js/src/lazyload.js',
	},
	output: {
		filename: '[name].js',
		path: __dirname + '/assets/js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: [
					{
						loader: 'babel-loader',
						query: {
							presets: [
								'@babel/env',
								'@wordpress/default',
							],
						},
					},
					{
						loader: 'eslint-loader',
						options: {
							failOnError: true,
							formatter: require( 'eslint' ).CLIEngine.getFormatter( 'stylish' ),
						},
					},
				],
			},
		],
	},
};
