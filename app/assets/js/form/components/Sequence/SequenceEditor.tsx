import React, { useState, useCallback } from 'react'

import { updateMature } from '../../src/reducer'
import { InteractorI, Mature, Chain } from '../../src/types'
import { useAction, useInteractorSelector } from '../../src/hooks'

import { ChainsFormGroup } from './ChainsFormGroup'
import { MatureProteinList } from './MatureProteinList'
import { CoordinateField } from '../Shared/CoordinateField'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { SubsequenceFormGroup } from '../Shared/SubsequenceFormGroup'

const natsort = (a: string, b: string) => a.localeCompare(b, undefined, {
    numeric: true,
    sensitivity: 'base'
})

type SequenceEditorProps = {
    i: InteractorI
    sequence: string
    matures: Mature[]
    chains: Chain[]
    hints: string[]
}

export const SequenceEditor: React.FC<SequenceEditorProps> = ({ i, sequence, matures, chains, hints }) => {
    const name = useInteractorSelector(i, state => state.name)
    const start = useInteractorSelector(i, state => state.start)
    const stop = useInteractorSelector(i, state => state.stop)

    const [sname, setSName] = useState<string>(name)
    const [sstart, setSStart] = useState<number | null>(start)
    const [sstop, setSStop] = useState<number | null>(stop)

    const names = matures.map(m => m.name)

    const availableHints = hints.filter(hint => matures.filter(m => m.name === hint).length === 0)

    const isNameSet = sname.trim().length > 0
    const isNameWellFormatted = sname.match(/^[^\s]+$/) != null
    const doesNameExist = matures.filter(m => m.name == sname).length > 0

    const areCoordinatesSet = sstart != null && sstop != null
    const areCoordinatesWellFormatted = sstart != null && sstop != null && sstart <= sstop
    const doCoordinatesExist = matures.filter(m => m.start == sstart && m.stop == sstop).length > 0

    const doesMatureExist = matures.filter(m => m.name == sname && m.start == sstart && m.stop == sstop).length > 0

    const isNameValid = doesMatureExist || isNameWellFormatted && !doCoordinatesExist

    const areCoordinatesValid = doesMatureExist || areCoordinatesWellFormatted && !doesNameExist

    const isMatureValid = isNameValid && areCoordinatesValid

    const selectHint = (hint: string) => {
        setSName(hint)
        setSStart(null)
        setSStop(null)
    }

    const setCoordinates = (start: number, stop: number) => {
        setSStart(start)
        setSStop(stop)
    }

    return (
        <React.Fragment>
            <MatureProteinListSection i={i} matures={matures} />
            <HintList hints={availableHints} select={selectHint} />
            <div className="row">
                <div className="col-3">
                    <NameInput value={sname} update={setSName} valid={!isNameSet || isNameValid} existing={names} />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={sstart}
                        update={setSStart}
                        max={sequence.length}
                        valid={!areCoordinatesSet || areCoordinatesValid}
                        placeholder="Start"
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={sstop}
                        update={setSStop}
                        max={sequence.length}
                        valid={!areCoordinatesSet || areCoordinatesValid}
                        placeholder="Stop"
                    />
                </div>
                <div className="col-3">
                    <ValidateButton i={i} name={sname} start={sstart} stop={sstop} disabled={!isMatureValid}>
                        Validate
                    </ValidateButton>
                </div>
            </div>
            {chains.length == 0 && (
                <ChainsFormGroup chains={chains} update={setCoordinates}>
                    Extract coordinates
                </ChainsFormGroup>
            )}
            <SubsequenceFormGroup sequence={sequence} update={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} update={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <FullLengthButton sequence={sequence} update={setCoordinates}>
                        Set to full length
                    </FullLengthButton>
                </div>
            </div>
        </React.Fragment>
    )
}

type MatureProteinListSectionProps = {
    i: InteractorI
    matures: Mature[]
}

const MatureProteinListSection: React.FC<MatureProteinListSectionProps> = ({ i, matures }) => {
    if (matures.length === 0) {
        return <p>No sequence defined on this uniprot entry yet.</p>
    }

    return (
        <React.Fragment>
            <p>
                Existing sequences on this uniprot entry:
            </p>
            <MatureProteinList i={i} matures={matures} />
        </React.Fragment>
    )
}

type HintListProps = {
    hints: string[],
    select: (hint: string) => void
}

const HintList: React.FC<HintListProps> = ({ hints, select }) => {
    if (hints.length === 0) {
        return <p>No existing name for this taxon yet.</p>
    }

    return (
        <React.Fragment>
            <p>Existing names for this taxon:</p>
            <ul className="list-inline">
                {hints.sort(natsort).map((hint, i) => (
                    <li key={i} className="list-inline-item mb-1">
                        <button className="btn btn-sm btn-outline-danger" onClick={() => select(hint)}>{hint}</button>
                    </li>
                ))}
            </ul>
        </React.Fragment>
    )
}

type NameInputProps = {
    value: string
    existing: string[]
    valid?: boolean
    placeholder?: string
    update: (value: string) => void
}

const NameInput: React.FC<NameInputProps> = ({ value, existing, valid = true, placeholder = 'Name', update }) => {
    const classes = 'form-control' + (valid ? '' : ' is-invalid')

    const supdate = useCallback((svalue: string) => {
        const found = existing.filter(name => name.toLowerCase() === svalue.toLowerCase())

        const value = found.length === 0 ? svalue : found[0]

        update(value)
    }, [update, existing])

    return (
        <input
            type="text"
            className={classes}
            placeholder={placeholder}
            value={value}
            onChange={e => supdate(e.target.value)}
        />
    )
}

type FullLengthButtonProps = {
    sequence: string
    update: (start: number, stop: number) => void
}

const FullLengthButton: React.FC<FullLengthButtonProps> = ({ sequence, update, children }) => (
    <button type="button" className="btn btn-block btn-info" onClick={() => update(1, sequence.length)}>
        {children}
    </button>
)

type ValidateButtonProps = {
    i: InteractorI
    name: string
    start: number | null
    stop: number | null
    disabled?: boolean
}

const ValidateButton: React.FC<ValidateButtonProps> = ({ i, name, start, stop, disabled = false, children }) => {
    const update = useAction(updateMature)

    const supdate = useCallback(() => {
        if (start && stop) {
            update({ i, mature: { name, start, stop } })
        }
    }, [i, name, start, stop, update])

    return (
        <button type="button" className="btn btn-block btn-primary" onClick={() => supdate()} disabled={disabled}>
            {children}
        </button>
    )
}
