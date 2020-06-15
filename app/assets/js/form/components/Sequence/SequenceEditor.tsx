import React, { useState } from 'react'

import { Mature, Chain } from '../../src/types'

import { ChainsFormGroup } from './ChainsFormGroup'
import { MatureProteinList } from './MatureProteinList'
import { CoordinateField } from '../Shared/CoordinateField'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { SubsequenceFormGroup } from '../Shared/SubsequenceFormGroup'

type Current = {
    name: string,
    start: number | null,
    stop: number | null,
}

type Props = {
    sequence: string,
    name: string,
    start: number | null,
    stop: number | null,
    matures: Mature[],
    chains: Chain[],
    update: (mature: Mature) => void,
}

const isMature = (current: Current): current is Mature => {
    return current.start != null && current.stop != null
}

export const SequenceEditor: React.FC<Props> = ({ sequence, name, start, stop, matures, chains, update }) => {
    const [current, setCurrent] = useState<Current>({ name: name, start: start, stop: stop })

    const isNameSet = current.name.trim() != ''

    const isNameWellFormatted = current.name.trim().match(/^[^\s]+$/)

    const areCoordinatesSet = current.start != null && current.stop != null

    const areCoordinatesWellFormatted = current.start != null && current.stop != null
        && current.start <= current.stop

    const doesNameExist = matures.filter(m => {
        return m.name.trim().toLowerCase() == current.name.trim().toLowerCase()
    }).length > 0

    const doCoordinatesExist = matures.filter(m => {
        return m.start == current.start && m.stop == current.stop
    }).length > 0

    const doesMatureExist = matures.filter(m => {
        return m.name.trim().toLowerCase() == current.name.trim().toLowerCase()
            && m.start == current.start
            && m.stop == current.stop
    }).length > 0

    const isNameValid = doesMatureExist || !isNameSet || isNameWellFormatted && !doesNameExist

    const areCoordinatesValid = doesMatureExist || !areCoordinatesSet || areCoordinatesWellFormatted && !doCoordinatesExist

    const isMatureValid = doesMatureExist ||
        isNameSet && isNameWellFormatted && !doesNameExist &&
        areCoordinatesSet && areCoordinatesWellFormatted && !doCoordinatesExist

    const setName = (name: string) => setCurrent({ name: name, start: current.start, stop: current.stop })
    const setStart = (start: number | null) => setCurrent({ name: current.name, start: start, stop: current.stop })
    const setStop = (stop: number | null) => setCurrent({ name: current.name, start: current.start, stop: stop })

    const setCoordinates = (start: number, stop: number) => setCurrent({
        name: current.name,
        start: start,
        stop: stop,
    })

    const setFullLength = () => setCoordinates(1, sequence.length)

    const submit = () => { if (isMature(current)) { update(current) } }

    return (
        <React.Fragment>
            {matures.length == 0
                ? <p>No sequence defined on this uniprot entry yet.</p>
                : (
                    <React.Fragment>
                        <p>
                            Existing sequences on this uniprot entry:
                        </p>
                        <MatureProteinList matures={matures} select={update} />
                    </React.Fragment>
                )
            }
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className={'form-control' + (isNameValid ? '' : ' is-invalid')}
                        placeholder="Name"
                        value={current.name}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={current.start}
                        set={setStart}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                        placeholder="Start"
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={current.stop}
                        set={setStop}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                        placeholder="Stop"
                    />
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => submit()}
                        disabled={!isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            {chains.length == 0 ? null : (
                <ChainsFormGroup chains={chains} set={setCoordinates}>
                    Extract coordinates
                </ChainsFormGroup>
            )}
            <SubsequenceFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={e => setFullLength()}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}