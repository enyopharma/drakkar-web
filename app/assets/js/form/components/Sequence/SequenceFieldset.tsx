import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEdit } from '@fortawesome/free-solid-svg-icons/faEdit'

import { editMature } from '../../src/reducer'
import { proteins as api } from '../../src/api'
import { InteractorI, Resource, Protein, Interactor } from '../../src/types'
import { useInteractorSelector, useAction } from '../../src/hooks'

const SequenceEditor = React.lazy(() => import('./SequenceEditor').then(m => ({ default: m.SequenceEditor })))
const SequenceTextarea = React.lazy(() => import('./SequenceTextarea').then(m => ({ default: m.SequenceTextarea })))
const SequenceFormGroup = React.lazy(() => import('./SequenceFormGroup').then(m => ({ default: m.SequenceFormGroup })))
const SequenceImg = React.lazy(() => import('../Shared/SequenceImg').then(m => ({ default: m.SequenceImg })))

type SequenceFieldsetProps = {
    i: InteractorI
}

export const SequenceFieldset: React.FC<SequenceFieldsetProps> = ({ i }) => {
    const protein_id = useInteractorSelector(i, state => state.protein_id)

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

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <SequenceFormGroup i={i} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceTextarea i={i} sequence={protein.sequence} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <MatureSequenceImg i={i} protein={protein} />
                </div>
                <div className="col-1">
                    <EditButton i={i}>
                        <FontAwesomeIcon icon={faEdit} />
                    </EditButton>
                </div>
            </div>
            <SequenceEditorToggle i={i} protein={protein} />
        </React.Fragment>
    )
}

type MatureSequenceImgProps = {
    i: InteractorI
    protein: Protein
}

const MatureSequenceImg: React.FC<MatureSequenceImgProps> = ({ i, protein }) => {
    const type = useInteractorSelector(i, state => state.type)
    const start = useInteractorSelector(i, state => state.start)
    const stop = useInteractorSelector(i, state => state.stop)

    return <SequenceImg type={type} start={start} stop={stop} length={protein.sequence.length} />
}

type SequenceEditorToggleProps = {
    i: InteractorI
    protein: Protein
}

const SequenceEditorToggle: React.FC<SequenceEditorToggleProps> = ({ i, protein }) => {
    const editing = useInteractorSelector(i, state => state.editing)

    if (!editing) return null

    return (
        <SequenceEditor
            i={i}
            sequence={protein.sequence}
            chains={protein.chains}
            matures={protein.matures}
        />
    )
}

type EditButtonProps = {
    i: InteractorI
}

const EditButton: React.FC<EditButtonProps> = ({ i, children }) => {
    const type = useInteractorSelector(i, state => state.type)
    const editing = useInteractorSelector(i, state => state.editing)
    const processing = useInteractorSelector(i, state => state.processing)
    const edit = useAction(editMature)

    return (
        <button
            className="btn btn-block btn-warning"
            onClick={() => edit({ i })}
            disabled={type == 'h' || editing || processing}
        >
            {children}
        </button>
    )
}
