<?php

namespace JMS\DiExtraBundle\Annotation;

@trigger_error(sprintf('%s is deprecated since version 1.7 and will be removed in 2.0. Use %s instead.', __NAMESPACE__.'\AfterSetup', __NAMESPACE__.'\Call'), E_USER_DEPRECATED);

/**
 * This class does not work and is only kept for BC. Do not use it.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @deprecated since 1.7, to be removed in 2.0. Use {@link Call} instead.
 */
final class AfterSetup
{
}
