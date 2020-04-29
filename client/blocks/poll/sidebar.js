/**
 * External dependencies
 */
import React from 'react';

/**
 * WordPress dependencies
 */
import {
	Button,
	CheckboxControl,
	ExternalLink,
	PanelBody,
	SelectControl,
	TextareaControl,
	TextControl,
} from '@wordpress/components';
import {
	InspectorControls,
	URLInput,
	PanelColorSettings,
	ContrastChecker,
} from '@wordpress/block-editor';
import { includes } from 'lodash';

/**
 * Internal dependencies
 */
import { ConfirmMessageType, FontFamilyType } from './constants';
import { __ } from 'lib/i18n';

const SideBar = ( {
	attributes,
	setAttributes,
	fallbackBackgroundColor,
	fallbackTextColor,
	fallbackSubmitButtonBackgroundColor,
	fallbackSubmitButtonTextColor,
} ) => {
	const handleChangeTitle = ( title ) => setAttributes( { title } );

	const handleChangeConfirmMessageType = ( type ) =>
		includes( ConfirmMessageType, type ) &&
		setAttributes( { confirmMessageType: type } );

	const handleChangeCustomConfirmMessage = ( customConfirmMessage ) =>
		setAttributes( { customConfirmMessage } );

	const handleChangeRedirectAddress = ( redirectAddress ) =>
		setAttributes( { redirectAddress } );

	const handleChangeTextColor = ( textColor ) =>
		setAttributes( { textColor } );

	const handleChangeBackgroundColor = ( backgroundColor ) =>
		setAttributes( { backgroundColor } );

	const handleChangeFontFamily = ( font ) =>
		includes( FontFamilyType, font ) &&
		setAttributes( { fontFamily: font } );

	const handleChangeHasCaptchaProtection = ( hasCaptchaProtection ) =>
		setAttributes( { hasCaptchaProtection } );

	const handleChangeHasOneResponsePerComputer = (
		hasOneResponsePerComputer
	) => setAttributes( { hasOneResponsePerComputer } );

	const handleChangeHasRandomOrderOfAnswers = ( hasRandomOrderOfAnswers ) =>
		setAttributes( { hasRandomOrderOfAnswers } );

	const handleChangeSubmitButtonTextColor = ( submitButtonTextColor ) =>
		setAttributes( { submitButtonTextColor } );

	const handleChangeSubmitButtonBackgroundColor = (
		submitButtonBackgroundColor
	) => setAttributes( { submitButtonBackgroundColor } );

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Data Settings' ) }>
				<p>
					{ __( 'Manage and view the poll results on: ' ) }
					<ExternalLink href="https://www.crowdsignal.com">
						crowdsignal.com
					</ExternalLink>
				</p>

				<p>
					<Button
						href="https://app.crowdsignal.com/polls/null/results"
						isDefault
						target="_blank"
					>
						{ __( 'View Results' ) }
					</Button>
				</p>

				<TextControl
					value={ attributes.title }
					label={ __( 'Title of the Poll Block' ) }
					onChange={ handleChangeTitle }
				/>

				<p className="wp-block-crowdsignal-forms__more-info-link">
					<ExternalLink
						href="http://www.crowdsignal.com"
						className="wp-block-crowdsiglan-forms__more-info-link-text"
					>
						{ __( 'What is Crowdsignal?' ) }
					</ExternalLink>
				</p>
			</PanelBody>
			<PanelBody title={ __( 'Confirmation Message' ) }>
				<SelectControl
					value={ attributes.confirmMessageType }
					label={ __( 'On Submission' ) }
					options={ [
						{
							label: __( 'Show Results' ),
							value: ConfirmMessageType.RESULTS,
						},
						{
							label: __( 'Show "Thank You" message' ),
							value: ConfirmMessageType.THANK_YOU,
						},
						{
							label: __( 'Show a custom text message' ),
							value: ConfirmMessageType.CUSTOM_TEXT,
						},
						{
							label: __( 'Redirect to another webpage' ),
							value: ConfirmMessageType.REDIRECT,
						},
					] }
					onChange={ handleChangeConfirmMessageType }
				/>

				{ ConfirmMessageType.CUSTOM_TEXT ===
					attributes.confirmMessageType && (
					<TextareaControl
						value={ attributes.customConfirmMessage }
						label={ __( 'Message Text' ) }
						placeholder={ __( 'Thank you for your submission!' ) }
						onChange={ handleChangeCustomConfirmMessage }
					/>
				) }

				{ ConfirmMessageType.REDIRECT ===
					attributes.confirmMessageType && (
					<URLInput
						className="wp-block-crowdsignal-forms__redirect-url"
						value={ attributes.redirectAddress }
						label={ __( 'Redirect Address' ) }
						onChange={ handleChangeRedirectAddress }
					/>
				) }
			</PanelBody>
			<PanelColorSettings
				title={ __( 'Color Settings' ) }
				initialOpen={ false }
				colorSettings={ [
					{
						value: attributes.textColor,
						onChange: handleChangeTextColor,
						label: __( 'Text Color' ),
					},
					{
						value: attributes.backgroundColor,
						onChange: handleChangeBackgroundColor,
						label: __( 'Background Color' ),
					},
					{
						value: attributes.submitButtonTextColor,
						onChange: handleChangeSubmitButtonTextColor,
						label: __( 'Submit Button Text Color' ),
					},
					{
						value: attributes.submitButtonBackgroundColor,
						onChange: handleChangeSubmitButtonBackgroundColor,
						label: __( 'Submit Button Background Color' ),
					},
				] }
			/>
			<ContrastChecker
				textColor={ attributes.textColor }
				backgroundColor={ attributes.backgroundColor }
				fallbackBackgroundColor={ fallbackBackgroundColor }
				fallbackTextColor={ fallbackTextColor }
			/>
			<ContrastChecker
				textColor={ attributes.submitButtonTextColor }
				backgroundColor={ attributes.submitButtonBackgroundColor }
				fallbackBackgroundColor={ fallbackSubmitButtonBackgroundColor }
				fallbackTextColor={ fallbackSubmitButtonTextColor }
			/>
			<PanelBody title={ __( 'Text Settings' ) } initialOpen={ false }>
				<SelectControl
					value={ attributes.fontFamily }
					label={ __( 'Choose Font-Family' ) }
					options={ [
						{
							label: __( 'Default Theme Font' ),
							value: FontFamilyType.THEME_DEFAULT,
						},
						{
							label: __( 'Comic Sans' ),
							value: FontFamilyType.COMIC_SANS,
						},
					] }
					onChange={ handleChangeFontFamily }
				/>
			</PanelBody>
			<PanelBody title={ __( 'Answer Settings' ) }>
				<CheckboxControl
					checked={ attributes.hasCaptchaProtection }
					label={ __( 'Enable Captcha Protection' ) }
					onChange={ handleChangeHasCaptchaProtection }
				/>
				<CheckboxControl
					checked={ attributes.hasOneResponsePerComputer }
					label={ __( 'One Response Per Computer' ) }
					onChange={ handleChangeHasOneResponsePerComputer }
				/>
				<CheckboxControl
					checked={ attributes.hasRandomOrderOfAnswers }
					label={ __( 'Random Order of Answers' ) }
					onChange={ handleChangeHasRandomOrderOfAnswers }
				/>
			</PanelBody>
		</InspectorControls>
	);
};

export default SideBar;
