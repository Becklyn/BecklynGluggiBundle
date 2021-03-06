import {h, JSX} from "preact";
import {useState} from "preact/hooks";
import {Gluggi} from "../@types/gluggi";
import {ComponentLink} from "./ComponentLink";
import {GluggiRouter} from "../lib/GluggiRouter";
import {Icon} from "./Icon";


export interface ComponentUsagesProps
{
    dependencies: Gluggi.Dependencies;
    usages: Gluggi.Dependencies;
    router: GluggiRouter;
}


/**
 * Renders a list of references
 */
function renderList (dependencies: Gluggi.Dependencies, showTransitive: boolean, router: GluggiRouter) : JSX.Element
{
    if (!dependencies.direct.length && (!showTransitive || !dependencies.transitive.length))
    {
        return <div class="gluggi-dependencies-empty">—</div>;
    }

    return (
        <ul class="gluggi-references-component-list">
            {dependencies.direct.map(component => (
                <li>
                    <ComponentLink component={component} router={router} />
                </li>
            ))}
            {showTransitive && dependencies.transitive.map(component => (
                <li>
                    <ComponentLink component={component} router={router}>
                        <Icon name="transitive" title="transitive dependency" />
                    </ComponentLink>
                </li>
            ))}
        </ul>
    );
}


/**
 * The complete references implementation
 */
export function ComponentReferences (props: ComponentUsagesProps): JSX.Element
{
    let [open, setOpen] = useState(false);
    let [transitive, setTransitive] = useState(false);
    let hasTransitiveDependencies = (props.dependencies.transitive.length + props.usages.transitive.length) > 0;

    let dependenciesCount = props.dependencies.direct.length;
    let usagesCount = props.usages.direct.length;

    if (transitive)
    {
        dependenciesCount += props.dependencies.transitive.length;
        usagesCount += props.usages.transitive.length;
    }

    return (
        <div class={`gluggi-action-usages ${open ? "is-open" : ""}`}>
            <button type="button" class="gluggi-action" onClick={() => setOpen(!open)}>
                <Icon name="usages" />
                <span class="gluggi-action-label">References</span>
            </button>
            {open && (
                <div class="gluggi-usages-overview">
                    {hasTransitiveDependencies && (
                        <button type="button" class="gluggi-usages-transitive-toggle" onClick={() => setTransitive(!transitive)}>
                            <Icon name="transitive" title="transitive dependency" />
                            {transitive ? "hide transitive" : "show transitive"}
                        </button>
                    )}
                    <div class="gluggi-usages-list">
                        <h3 class="gluggi-references-headline">{`Dependencies (${dependenciesCount})`}</h3>
                        {renderList(props.dependencies, transitive, props.router)}
                    </div>

                    <div class="gluggi-usages-list">
                        <h3 class="gluggi-references-headline">{`Usages (${usagesCount})`}</h3>
                        {renderList(props.usages, transitive, props.router)}
                    </div>
                </div>
            )}
        </div>
    );
}
