import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEdit } from '@fortawesome/free-solid-svg-icons/faEdit'

import { editMature } from '../../src/reducer'
import { proteins as api } from '../../src/api'
import { InteractorI, Resource, Protein } from '../../src/types'
import { useInteractorSelector, useAction } from '../../src/hooks'

const SequenceEditor = React.lazy(() => import('./SequenceEditor').then(m => ({ default: m.SequenceEditor })))
const SequenceTextarea = React.lazy(() => import('./SequenceTextarea').then(m => ({ default: m.SequenceTextarea })))
const SequenceFormGroup = React.lazy(() => import('./SequenceFormGroup').then(m => ({ default: m.SequenceFormGroup })))
const SequenceImg = React.lazy(() => import('../Shared/SequenceImg').then(m => ({ default: m.SequenceImg })))

type SequenceFieldsetProps = {
    i: InteractorI
}

export const SequenceFieldset: React.FC<SequenceFieldsetProps> = ({ i }) => {
    const { protein_id, ...props } = useInteractorSelector(i, state => state)

    const resource = protein_id === null ? null : api.select(protein_id)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Sequence</legend>
                {resource == null
                    ? <p>Please select an uniprot entry first.</p>
                    : <SequenceSection i={i} resource={resource} />
                }
            </fieldset>
        </React.Suspense>
    )
}

type SequenceSectionProps = {
    i: InteractorI
    resource: Resource<Protein>
}

export const SequenceSection: React.FC<SequenceSectionProps> = ({ i, resource }) => {
    const protein = resource.read()

    const { type, name, start, stop, editing, processing } = useInteractorSelector(i, state => state)
    const edit = useAction(editMature)

    const valid = !editing
    const enabled = type == 'v' && !editing && !processing

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <SequenceFormGroup name={name} start={start} stop={stop} valid={valid} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceTextarea sequence={protein.sequence} start={start} stop={stop} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceImg type={type} start={start} stop={stop} length={protein.sequence.length} />
                </div>
                <div className="col-1">
                    <button
                        className="btn btn-block btn-warning"
                        onClick={() => edit({ i })}
                        disabled={!enabled}
                    >
                        <FontAwesomeIcon icon={faEdit} />
                    </button>
                </div>
            </div>
            {editing && (
                <SequenceEditor
                    i={i}
                    name={name}
                    start={start}
                    stop={stop}
                    sequence={protein.sequence}
                    chains={protein.chains}
                    matures={protein.matures}
                />
            )}
        </React.Fragment>
    )
}
