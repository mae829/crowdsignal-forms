/**
 * External dependencies
 */
import React from 'react';
import { map } from 'lodash';

/**
 * WordPress dependencies
 */
import { BlockAlignmentToolbar, BlockControls } from '@wordpress/block-editor';
import { Toolbar } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { __ } from 'lib/i18n';

const multipleChoiceControls = [
	{
		icon: 'list-view',
		title: __( 'Radio (Choose one)' ),
		value: false,
	},
	{
		icon: 'list-view',
		title: __( 'Checkbox (Choose many)' ),
		value: true,
	},
];

const PollToolbar = ( { attributes, setAttributes } ) => {
	const multipleChoiceToolbar = map( multipleChoiceControls, ( button ) => ( {
		...button,
		isActive: button.value === attributes.isMultipleChoice,
		onClick: () => setAttributes( { isMultipleChoice: button.value } ),
	} ) );

	const handleChangeAlignment = ( blockAlignment ) =>
		setAttributes( { blockAlignment } );

	return (
		<BlockControls>
			<Toolbar controls={ multipleChoiceToolbar } />
			<BlockAlignmentToolbar
				value={ attributes.blockAlignment }
				onChange={ handleChangeAlignment }
			/>
		</BlockControls>
	);
};

export default PollToolbar;
