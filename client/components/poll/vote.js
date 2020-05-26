/**
 * External dependencies
 */
import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { includes, map, without } from 'lodash';

/**
 * Internal dependencies
 */
import { __ } from 'lib/i18n';
import PollAnswer from './answer';

const PollVote = ( {
	answers,
	isMultipleChoice,
	onSubmit,
	submitButtonLabel,
} ) => {
	const [ selected, setSelected ] = useState( [] );

	const handleSelect = ( answerId ) => {
		if ( ! isMultipleChoice ) {
			return setSelected( [ answerId ] );
		}

		if ( includes( selected, answerId ) ) {
			return setSelected( without( selected, answerId ) );
		}

		setSelected( [ ...selected, answerId ] );
	};

	const handleSubmit = ( event ) => {
		event.preventDefault();

		onSubmit( selected );
	};

	return (
		<form
			className="wp-block-crowdsignal-forms-poll__form"
			onSubmit={ handleSubmit }
		>
			<div className="wp-block-crowdsignal-forms__options">
				{ map( answers, ( answer, index ) => (
					<PollAnswer
						key={ `poll-answer-${ index }` }
						isMultipleChoice={ isMultipleChoice }
						isSelected={ includes( selected, answer.answerId ) }
						onSelect={ handleSelect }
						{ ...answer }
					/>
				) ) }
			</div>

			<div className="wp-block-crowdsignal-forms-poll__actions">
				<div className="wp-block-button">
					<button
						type="submit"
						className="wp-block-button__link wp-block-crowdsignal-forms-poll__submit-button"
					>
						{ submitButtonLabel || __( 'Submit' ) }
					</button>
				</div>
			</div>
		</form>
	);
};

PollVote.propTypes = {
	answers: PropTypes.array.isRequired,
	isMultipleChoice: PropTypes.bool,
	onSubmit: PropTypes.func.isRequired,
	submitButtonLabel: PropTypes.string.isRequired,
};

export default PollVote;